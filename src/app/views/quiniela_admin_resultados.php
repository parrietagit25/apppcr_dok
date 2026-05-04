<?php
if (!isset($_SESSION['code'])) {
    header('Location: salir.php');
    exit();
}
include __DIR__ . '/header.php';
include __DIR__ . '/quiniela_include_banderas.php';
$qBase = rtrim(BASE_URL_CONTROLLER, '/') . '/QuinielaController.php';
$mapEquipoIsoJson = json_encode($quinielaIsoPorEquipoId ?? [], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css">
<style>
    .quiniela-ts-res .ts-control { min-height: 31px; font-size: 0.875rem; }
    .ts-dropdown .option { padding: 0.35rem 0.5rem; }
</style>

<div class="container mt-3 mb-5 pb-5">
    <div class="d-flex align-items-center mb-3 flex-wrap gap-2">
        <div class="bg-warning text-dark px-3 py-2 rounded-start">
            <i class="bi bi-pencil-square"></i> V-Resultados
        </div>
        <div class="bg-white text-muted px-3 py-2 rounded-end flex-grow-1">
            Ganador oficial por partido
        </div>
        <a href="<?php echo htmlspecialchars($qBase); ?>" class="btn btn-outline-secondary btn-sm">Menú quiniela</a>
    </div>

    <?php if ($mensaje !== '') { ?>
    <div class="alert alert-<?php echo htmlspecialchars($mensajeTipo); ?>"><?php echo htmlspecialchars($mensaje); ?></div>
    <?php } ?>

    <?php if ($totalPartidos === 0) { ?>
    <div class="alert alert-warning">No hay partidos. Configúrelos en V-Quiniela.</div>
    <?php } else { ?>
    <div class="table-responsive bg-white rounded shadow-sm">
        <table class="table table-sm align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Partido</th>
                    <th>Ganador</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($partidosAdmin as $p) {
                    $pid = (int) $p['id'];
                    $opts = $quinielaModel->candidatosOficialesGanador($pid);
                    ?>
                <tr>
                    <td><?php echo $pid; ?></td>
                    <td><?php echo $quinielaModel->etiquetaPartidoHtml($p); ?>
                        <?php if ($p['tipo'] === 'ganadores') { ?>
                        <div class="small text-muted">Entre ganadores: partidos #<?php echo (int) $p['src_partido_a_id']; ?> y #<?php echo (int) $p['src_partido_b_id']; ?></div>
                        <?php } ?>
                    </td>
                    <td>
                        <form method="post" action="<?php echo htmlspecialchars($qBase . '?v_resultados=1'); ?>" class="d-flex flex-wrap gap-1 align-items-center">
                            <input type="hidden" name="partido_id" value="<?php echo $pid; ?>">
                            <div class="quiniela-ts-res" style="min-width:12rem;">
                            <select class="form-select form-select-sm quiniela-ts-resultado-ganador" name="ganador_id" <?php echo count($opts) !== 2 ? 'disabled' : ''; ?>>
                                <option value="0">— Pendiente —</option>
                                <?php
                                if (count($opts) === 2) {
                                    foreach ($opts as $eid) {
                                        $eid = (int) $eid;
                                        $de = $quinielaModel->datosEquipo($eid);
                                        $nom = $de['nombre'] ?? $quinielaModel->nombreEquipo($eid);
                                        $isoAttr = htmlspecialchars((string) ($de['iso'] ?? ''), ENT_QUOTES, 'UTF-8');
                                        $sel = ((int) ($p['ganador_id'] ?? 0) === $eid) ? 'selected' : '';
                                        echo '<option value="' . $eid . '" data-iso="' . $isoAttr . '" ' . $sel . '>' . htmlspecialchars($nom) . '</option>';
                                    }
                                }
                                ?>
                            </select>
                            </div>
                            <?php if (count($opts) === 2) { ?>
                            <button type="submit" name="guardar_resultado_partido" value="1" class="btn btn-primary btn-sm">Guardar</button>
                            <?php } else { ?>
                            <span class="small text-muted">Defina antes el ganador de los partidos previos.</span>
                            <?php } ?>
                        </form>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <?php } ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof TomSelect === 'undefined') return;
    var ISO_EQUIPO = <?php echo $mapEquipoIsoJson; ?>;
    function normIso(iso) {
        iso = (iso || '').toString().toLowerCase().replace(/[^a-z]/g, '');
        return (iso.length === 2) ? iso : 'un';
    }
    function flagRow(iso, text, escape) {
        var i = normIso(iso);
        var u = 'https://flagcdn.com/w40/' + i + '.png';
        return '<div class="d-flex align-items-center">' +
            '<img class="flag-icon" src="' + u + '" width="20" height="15" alt="" loading="lazy" referrerpolicy="no-referrer">' +
            '<span>' + escape(text) + '</span></div>';
    }
    document.querySelectorAll('.quiniela-ts-resultado-ganador:not([disabled])').forEach(function (sel) {
        new TomSelect(sel, {
            allowEmptyOption: true,
            plugins: ['clear_button'],
            render: {
                option: function (data, escape) {
                    var iso = data.iso || (ISO_EQUIPO[String(data.value)] || '');
                    return flagRow(iso, data.text, escape);
                },
                item: function (data, escape) {
                    var iso = data.iso || (ISO_EQUIPO[String(data.value)] || '');
                    return flagRow(iso, data.text, escape);
                }
            }
        });
    });
});
</script>

<?php include __DIR__ . '/footer.php'; ?>
