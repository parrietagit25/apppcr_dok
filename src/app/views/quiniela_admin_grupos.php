<?php
if (!isset($_SESSION['code'])) {
    header('Location: salir.php');
    exit();
}
include __DIR__ . '/header.php';
include __DIR__ . '/quiniela_include_banderas.php';
$qBase = rtrim(BASE_URL_CONTROLLER, '/') . '/QuinielaController.php';
$nGrupos = count($gruposAdmin);
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css">
<style>
    .quiniela-ts-wrap .ts-control { min-height: 38px; border-radius: 0.375rem; }
    .quiniela-ts-wrap.form-select-sm .ts-control { min-height: 31px; font-size: 0.875rem; }
    .ts-dropdown .option { padding: 0.35rem 0.5rem; }
</style>

<div class="container mt-3 mb-5 pb-5">
    <div class="d-flex align-items-center mb-3 flex-wrap gap-2">
        <div class="bg-warning text-dark px-3 py-2 rounded-start">
            <i class="bi bi-diagram-3"></i> V-Quiniela
        </div>
        <div class="bg-white text-muted px-3 py-2 rounded-end flex-grow-1">
            Grupos y equipos
        </div>
        <a href="<?php echo htmlspecialchars($qBase); ?>" class="btn btn-outline-secondary btn-sm">Menú quiniela</a>
    </div>

    <?php if ($mensaje !== '') { ?>
    <div class="alert alert-<?php echo htmlspecialchars($mensajeTipo); ?>"><?php echo htmlspecialchars($mensaje); ?></div>
    <?php } ?>

    <div class="alert alert-info small mb-4">
        Cree cada grupo con <strong>4 equipos</strong> (12 grupos). Las banderas usan el código ISO de cada país.
        Los resultados oficiales por fase se registran en <strong>V-Resultados</strong>; no es necesario crear partidos.
    </div>

    <?php if ($nGrupos < 12) { ?>
    <div class="card shadow-sm mb-4">
        <div class="card-header fw-bold">Nuevo grupo (4 equipos)</div>
        <div class="card-body">
            <form method="post" action="<?php echo htmlspecialchars($qBase . '?v_quiniela=1'); ?>">
                <div class="row g-2">
                    <div class="col-md-2">
                        <label class="form-label small">Nº grupo (1–12)</label>
                        <select class="form-select form-select-sm" name="orden_grupo" required>
                            <?php
                            $usados = array_column($gruposAdmin, 'orden_grupo');
                            $hay = false;
                            for ($i = 1; $i <= 12; $i++) {
                                if (in_array($i, $usados, true)) {
                                    continue;
                                }
                                $hay = true;
                                echo '<option value="' . $i . '">' . $i . '</option>';
                            }
                            ?>
                        </select>
                        <?php if (!$hay) { ?><small class="text-danger">No hay números libres.</small><?php } ?>
                    </div>
                    <div class="col-md-10">
                        <label class="form-label small">Nombre del grupo</label>
                        <input type="text" class="form-control form-control-sm" name="nombre_grupo" maxlength="120" required placeholder="Grupo A">
                    </div>
                    <?php for ($n = 1; $n <= 4; $n++) { ?>
                    <div class="col-md-6">
                        <label class="form-label small">Equipo <?php echo $n; ?> (país)</label>
                        <select class="form-select form-select-sm quiniela-ts-pais-nuevo" name="equipo_<?php echo $n; ?>" id="quiniela_equipo_new_<?php echo $n; ?>" autocomplete="off" required>
                            <option value="">Buscar o escribir país…</option>
                            <?php foreach (quiniela_paises_mundial_lista() as $p) { ?>
                            <option value="<?php echo htmlspecialchars($p['nombre']); ?>" data-iso="<?php echo htmlspecialchars($p['iso']); ?>"><?php echo htmlspecialchars($p['nombre']); ?></option>
                            <?php } ?>
                        </select>
                        <label class="form-label small text-muted mt-1">ISO (2 letras, opcional si el país no está en la lista)</label>
                        <input type="text" class="form-control form-control-sm" name="equipo_iso_<?php echo $n; ?>" id="quiniela_iso_slot_<?php echo $n; ?>" maxlength="2" pattern="[A-Za-z]{0,2}" placeholder="Auto" title="Código ISO; se rellena al elegir país o escríbalo a mano">
                    </div>
                    <?php } ?>
                </div>
                <button type="submit" name="crear_grupo_quiniela" value="1" class="btn btn-warning btn-sm mt-3">Crear grupo</button>
            </form>
        </div>
    </div>
    <?php } ?>

    <h6 class="section-title">Grupos registrados (<?php echo (int) $nGrupos; ?>)</h6>
    <?php foreach ($gruposAdmin as $g) {
        $gid = (int) $g['id'];
        ?>
    <div class="bg-white rounded shadow-sm p-3 mb-4">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
            <div>
                <strong>Grupo <?php echo (int) $g['orden_grupo']; ?>:</strong> <?php echo htmlspecialchars($g['nombre_grupo']); ?>
            </div>
            <form method="post" action="<?php echo htmlspecialchars($qBase . '?v_quiniela=1'); ?>" class="d-inline" onsubmit="return confirm('¿Eliminar grupo completo?');">
                <input type="hidden" name="grupo_id" value="<?php echo $gid; ?>">
                <button type="submit" name="eliminar_grupo_quiniela" value="1" class="btn btn-outline-danger btn-sm">Eliminar grupo</button>
            </form>
        </div>
        <ul class="small mb-0">
            <?php foreach ($g['equipos'] as $eq) { ?>
            <li class="d-flex align-items-center flex-wrap gap-1"><?php echo quiniela_flag_icon_html($eq['iso'] ?? null, (string) $eq['nombre'], true); ?> <span class="text-muted">(id <?php echo (int) $eq['id']; ?>)</span></li>
            <?php } ?>
        </ul>
    </div>
    <?php } ?>
</div>

<?php
$mapNombreIsoJson = json_encode(quiniela_paises_mapa_nombre_a_iso(), JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
$mapEquipoIsoJson = json_encode($quinielaIsoPorEquipoId ?? [], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
?>

<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof TomSelect === 'undefined') return;

    var ISO_PAIS = <?php echo $mapNombreIsoJson; ?>;

    function quinielaFlagHtml(iso) {
        iso = (iso || '').toString().toLowerCase().replace(/[^a-z]/g, '');
        if (!iso || iso.length !== 2) {
            iso = 'un';
        }
        var u = 'https://flagcdn.com/w40/' + iso + '.png';
        return '<img class="flag-icon me-1" width="20" height="15" alt="" src="' + u + '" loading="lazy" referrerpolicy="no-referrer">';
    }

    function rowConBandera(data, escape, mapPais) {
        var label = (data.text !== undefined && data.text !== null) ? String(data.text) : String(data.value || '');
        var iso = '';
        if (mapPais && data.value && mapPais[data.value]) {
            iso = mapPais[data.value];
        }
        return '<div class="d-flex align-items-center py-1">' + quinielaFlagHtml(iso) + '<span>' + escape(label) + '</span></div>';
    }

    document.querySelectorAll('.quiniela-ts-pais-nuevo').forEach(function (sel) {
        var m = sel.id.match(/quiniela_equipo_new_(\d+)/);
        var slot = m ? m[1] : null;
        var isoInput = slot ? document.getElementById('quiniela_iso_slot_' + slot) : null;
        function syncIsoDesdeNombre(ts) {
            if (!isoInput) return;
            var v = (ts && ts.getValue) ? ts.getValue() : '';
            if (!v) return;
            var fromMap = ISO_PAIS[v];
            if (fromMap) {
                isoInput.value = String(fromMap).toLowerCase().substring(0, 2);
            }
        }
        new TomSelect(sel, {
            allowEmptyOption: true,
            create: function (input) {
                var t = (input || '').trim();
                if (t.length < 2) return null;
                return { value: t, text: t };
            },
            createOnBlur: true,
            sortField: { field: 'text', direction: 'asc' },
            maxOptions: 500,
            plugins: ['clear_button'],
            placeholder: 'Buscar país…',
            onItemAdd: function () { syncIsoDesdeNombre(this); },
            onChange: function () { syncIsoDesdeNombre(this); },
            render: {
                option: function (data, escape) {
                    return rowConBandera(data, escape, ISO_PAIS);
                },
                item: function (data, escape) {
                    return rowConBandera(data, escape, ISO_PAIS);
                }
            }
        });
    });
});
</script>

<?php include __DIR__ . '/footer.php'; ?>
