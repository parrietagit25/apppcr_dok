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
    <div class="alert alert-info">Aún no hay cartas de quiniela registradas.</div>
    <?php } else { ?>
    <div class="table-responsive bg-white rounded shadow-sm">
        <table class="table table-sm table-striped mb-0" id="tablaColabQuiniela">
            <thead class="table-light">
                <tr>
                    <th>Colaborador</th>
                    <th>Código</th>
                    <th>Status</th>
                    <th class="text-center">Aciertos</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($colaboradoresLista as $row) {
                    $st = $row['status'] ?? '';
                    if ($st === 'Ganador') {
                        $badge = 'warning text-dark';
                    } elseif ($st === 'Perdió') {
                        $badge = 'danger';
                    } elseif ($st === 'En juego') {
                        $badge = 'success';
                    } elseif ($st === 'Completada') {
                        $badge = 'primary';
                    } else {
                        $badge = 'secondary';
                    }
                    $puntos = (int) ($row['puntos'] ?? 0);
                    ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($row['codigo_empleado']); ?></td>
                    <td>
                        <span class="badge bg-<?php echo $badge; ?>">
                            <?php if ($st === 'Ganador') { ?><i class="bi bi-trophy-fill"></i> <?php } ?>
                            <?php echo htmlspecialchars($row['status']); ?>
                        </span>
                    </td>
                    <td class="text-center fw-semibold"><?php echo $puntos; ?></td>
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
            if (!data || !data.fases) {
                body.innerHTML = '<p class="text-muted">Sin datos.</p>';
            } else {
                titulo.textContent = 'Quiniela — ' + (data.nombre ? (data.nombre + ' · ') : '') + codigo;
                var cerrada = data.cerrada ? ' <span class="badge bg-secondary">Cerrada</span>' : '';
                var statusBadge = '';
                if (data.status === 'Ganador') {
                    statusBadge = ' <span class="badge bg-warning text-dark"><i class="bi bi-trophy-fill"></i> Ganador</span>';
                } else if (data.status) {
                    statusBadge = ' <span class="badge bg-secondary">' + esc(data.status) + '</span>';
                }
                var puntos = typeof data.puntos === 'number' ? data.puntos : 0;
                var html = '<div class="d-flex flex-wrap align-items-center gap-2 mb-3">'
                    + '<span class="badge bg-primary fs-6">Total aciertos: ' + puntos + '</span>'
                    + cerrada + statusBadge
                    + '</div>';
                if (data.campeon_espana) {
                    html += '<p class="small text-success mb-2"><i class="bi bi-check-circle"></i> Campeón elegido: España</p>';
                }
                data.fases.forEach(function (bloque) {
                    var ptsFase = 0;
                    if (data.puntos_por_fase && bloque.fase && data.puntos_por_fase[bloque.fase] != null) {
                        ptsFase = data.puntos_por_fase[bloque.fase];
                    }
                    html += '<h6 class="mt-3">' + esc(bloque.etiqueta || bloque.fase || '')
                        + ' <span class="badge bg-light text-dark border">' + ptsFase + ' acierto(s)</span></h6>';
                    if (bloque.grupos && bloque.grupos.length) {
                        bloque.grupos.forEach(function (gr) {
                            html += '<p class="mb-1 small text-muted">' + esc(gr.nombre_grupo || '') + '</p><ul class="small">';
                            (gr.equipos || []).forEach(function (it) {
                                html += '<li>' + (it.html || esc(it.nombre || '')) + '</li>';
                            });
                            html += '</ul>';
                        });
                    } else {
                        html += '<ul class="small">';
                        (bloque.equipos || []).forEach(function (it) {
                            html += '<li>' + (it.html || esc(it.nombre || '')) + '</li>';
                        });
                        html += '</ul>';
                    }
                });
                body.innerHTML = html;
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
