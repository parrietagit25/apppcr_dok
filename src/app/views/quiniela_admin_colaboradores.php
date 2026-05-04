<?php
if (!isset($_SESSION['code'])) {
    header('Location: salir.php');
    exit();
}
include __DIR__ . '/header.php';
include __DIR__ . '/quiniela_include_banderas.php';
$qBase = rtrim(BASE_URL_CONTROLLER, '/') . '/QuinielaController.php';
$jsonDetalle = json_encode($colaboradoresJson ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE);
?>

<div class="container mt-3 mb-5 pb-5">
    <div class="d-flex align-items-center mb-3 flex-wrap gap-2">
        <div class="bg-warning text-dark px-3 py-2 rounded-start">
            <i class="bi bi-people"></i> Colaboradores
        </div>
        <div class="bg-white text-muted px-3 py-2 rounded-end flex-grow-1">
            Quinielas registradas
        </div>
        <a href="<?php echo htmlspecialchars($qBase); ?>" class="btn btn-outline-secondary btn-sm">Menú quiniela</a>
    </div>

    <?php if (count($colaboradoresLista) === 0) { ?>
    <div class="alert alert-info">Aún no hay colaboradores con predicciones guardadas o quiniela cerrada.</div>
    <?php } else { ?>
    <div class="table-responsive bg-white rounded shadow-sm">
        <table class="table table-sm table-striped mb-0" id="tablaColabQuiniela">
            <thead class="table-light">
                <tr>
                    <th>Colaborador</th>
                    <th>Código</th>
                    <th>Status</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($colaboradoresLista as $row) {
                    $st = $row['status'] ?? '';
                    if ($st === 'Perdió') {
                        $badge = 'danger';
                    } elseif ($st === 'En juego') {
                        $badge = 'success';
                    } elseif ($st === 'Completada') {
                        $badge = 'primary';
                    } else {
                        $badge = 'warning text-dark';
                    }
                    ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($row['codigo_empleado']); ?></td>
                    <td><span class="badge bg-<?php echo $badge; ?>"><?php echo htmlspecialchars($row['status']); ?></span></td>
                    <td class="text-end">
                        <button type="button" class="btn btn-outline-primary btn-sm js-ver-quiniela" data-codigo="<?php echo htmlspecialchars($row['codigo_empleado']); ?>">
                            Ver quiniela
                        </button>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <?php } ?>
</div>

<div class="modal fade" id="modalDetalleQuiniela" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalQuinielaTitulo">Quiniela</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body" id="modalQuinielaBody"></div>
        </div>
    </div>
</div>

<script type="application/json" id="quiniela-detalle-data"><?php echo $jsonDetalle; ?></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var raw = document.getElementById('quiniela-detalle-data');
    if (!raw) return;
    var detalle = {};
    try { detalle = JSON.parse(raw.textContent || '{}'); } catch (e) { detalle = {}; }
    document.querySelectorAll('.js-ver-quiniela').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var codigo = btn.getAttribute('data-codigo');
            var data = detalle[codigo];
            var titulo = document.getElementById('modalQuinielaTitulo');
            var body = document.getElementById('modalQuinielaBody');
            if (!data || !data.predicciones) {
                body.innerHTML = '<p class="text-muted">Sin datos.</p>';
            } else {
                titulo.textContent = 'Quiniela — ' + (data.nombre ? (data.nombre + ' · ') : '') + codigo;
                var rows = data.predicciones.map(function (p) {
                    var desc = p.descripcion_html || esc(p.descripcion || '');
                    var pred = p.predicho_html || esc(p.predicho_nombre || '');
                    var off = p.resultado_html
                        ? p.resultado_html
                        : (p.resultado_nombre ? ('<strong>' + esc(p.resultado_nombre) + '</strong>') : '<span class="text-muted">Pendiente</span>');
                    return '<tr><td>' + esc(p.grupo_nombre || '') + '</td><td>' + desc + '</td><td>' + pred + '</td><td>' + off + '</td></tr>';
                }).join('');
                body.innerHTML = '<div class="table-responsive"><table class="table table-sm"><thead><tr><th>Grupo / fase</th><th>Partido</th><th>Predicción</th><th>Oficial</th></tr></thead><tbody>' + rows + '</tbody></table></div>';
            }
            var elModal = document.getElementById('modalDetalleQuiniela');
            if (typeof bootstrap !== 'undefined' && elModal) {
                bootstrap.Modal.getOrCreateInstance(elModal).show();
            }
        });
    });
    function esc(s) {
        if (!s) return '';
        var d = document.createElement('div');
        d.textContent = s;
        return d.innerHTML;
    }
});
</script>

<?php include __DIR__ . '/footer.php'; ?>
