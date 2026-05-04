<?php
if (!isset($_SESSION['code'])) {
    header('Location: salir.php');
    exit();
}
include __DIR__ . '/header.php';
include __DIR__ . '/quiniela_include_banderas.php';
$qBase = rtrim(BASE_URL_CONTROLLER, '/') . '/QuinielaController.php';

$ofIds = $adminOficialIdsPorFase ?? [];
$pools = $adminPoolOficial ?? [];
$ofGrupo = $oficialGruposPorId ?? [];

function quiniela_admin_eq_pool(array $equiposSelector, ?array $poolIds): array
{
    if ($poolIds === null || $poolIds === []) {
        return [];
    }
    $flip = array_flip(array_map('intval', $poolIds));
    $out = [];
    foreach ($equiposSelector as $eq) {
        if (isset($flip[(int) $eq['id']])) {
            $out[] = $eq;
        }
    }
    return $out;
}

?>
<div class="container mt-3 mb-5 pb-5">
    <div class="d-flex align-items-center mb-3 flex-wrap gap-2">
        <div class="bg-warning text-dark px-3 py-2 rounded-start">
            <i class="bi bi-pencil-square"></i> V-Resultados
        </div>
        <div class="bg-white text-muted px-3 py-2 rounded-end flex-grow-1">
            Registrar clasificados oficiales por fase
        </div>
        <a href="<?php echo htmlspecialchars($qBase); ?>" class="btn btn-outline-secondary btn-sm">Menú quiniela</a>
    </div>

    <?php if ($mensaje !== '') { ?>
    <div class="alert alert-<?php echo htmlspecialchars($mensajeTipo); ?>"><?php echo htmlspecialchars($mensaje); ?></div>
    <?php } ?>

    <div class="alert alert-info small mb-4">
        Guarde cada fase en orden: primero los 2 clasificados por grupo, luego 8 mejores terceros, y así sucesivamente hasta el campeón.
    </div>

    <h6 class="section-title"><?php echo htmlspecialchars(Quiniela::etiquetaFase(Quiniela::F_GRUPOS)); ?></h6>
    <form method="post" action="<?php echo htmlspecialchars($qBase . '?v_resultados=1'); ?>" class="mb-4" id="formOfGruposAdmin">
        <?php foreach ($gruposAdmin as $g) {
            $gid = (int) $g['id'];
            $prev = $ofGrupo[$gid] ?? [];
            ?>
        <div class="bg-white rounded shadow-sm p-3 mb-3">
            <div class="fw-bold mb-2"><?php echo htmlspecialchars($g['nombre_grupo']); ?> <span class="text-muted small">(Grupo <?php echo (int) $g['orden_grupo']; ?>)</span></div>
            <div class="row">
                <?php foreach ($g['equipos'] as $eq) {
                    $eid = (int) $eq['id'];
                    $chk = in_array($eid, $prev, true) ? ' checked' : '';
                    ?>
                <div class="col-6 col-md-3 mb-2">
                    <label class="form-check d-flex align-items-center">
                        <input type="checkbox" class="form-check-input js-of-grupo" name="of_grupo_<?php echo $gid; ?>[]" value="<?php echo $eid; ?>" data-grupo="<?php echo $gid; ?>"<?php echo $chk; ?>>
                        <span class="ms-2"><?php echo quiniela_flag_icon_html($eq['iso'] ?? null, (string) $eq['nombre'], true); ?></span>
                    </label>
                </div>
                <?php } ?>
            </div>
        </div>
        <?php } ?>
        <button type="submit" name="guardar_oficial_grupos" value="1" class="btn btn-warning btn-sm">Guardar clasificados por grupo</button>
    </form>

    <?php
    $poolMt = $pools['mejores_terceros'] ?? [];
    $eqMt = quiniela_admin_eq_pool($equiposSelector, $poolMt);
    $selMt = $ofIds[Quiniela::F_MEJORES_TERCEROS] ?? [];
    ?>
    <h6 class="section-title"><?php echo htmlspecialchars(Quiniela::etiquetaFase(Quiniela::F_MEJORES_TERCEROS)); ?></h6>
    <?php if (count($poolMt) < 12) { ?>
    <p class="text-muted small">Complete y guarde primero los clasificados oficiales por grupo (2 por cada uno de los 12 grupos).</p>
    <?php } else { ?>
    <form method="post" action="<?php echo htmlspecialchars($qBase . '?v_resultados=1'); ?>" class="bg-white rounded shadow-sm p-3 mb-4" id="formOfMt">
        <input type="hidden" name="fase_oficial" value="<?php echo htmlspecialchars(Quiniela::F_MEJORES_TERCEROS); ?>">
        <p class="small text-muted">Elija exactamente <strong>8</strong> equipos entre los terceros de cada grupo.</p>
        <div class="row">
            <?php foreach ($eqMt as $eq) {
                $eid = (int) $eq['id'];
                $iso = $eq['iso'] ?? quiniela_paises_iso_por_nombre((string) $eq['nombre']);
                $chk = in_array($eid, $selMt, true) ? ' checked' : '';
                ?>
            <div class="col-6 col-md-4 col-lg-3 mb-2">
                <label class="form-check d-flex align-items-center">
                    <input type="checkbox" class="form-check-input js-of-mt" name="equipo_oficial[]" value="<?php echo $eid; ?>"<?php echo $chk; ?>>
                    <span class="ms-2"><?php echo quiniela_flag_icon_html($iso, (string) $eq['nombre'], true); ?></span>
                </label>
            </div>
            <?php } ?>
        </div>
        <button type="submit" name="guardar_oficial_fase" value="1" class="btn btn-warning btn-sm mt-2">Guardar mejores terceros</button>
    </form>
    <script>
    (function () {
        var form = document.getElementById('formOfMt');
        if (!form) return;
        form.querySelectorAll('.js-of-mt').forEach(function (cb) {
            cb.addEventListener('change', function () {
                var n = form.querySelectorAll('.js-of-mt:checked').length;
                if (n > 8) { cb.checked = false; alert('Máximo 8 equipos.'); }
            });
        });
        form.addEventListener('submit', function (e) {
            if (form.querySelectorAll('.js-of-mt:checked').length !== 8) {
                e.preventDefault();
                alert('Debe elegir exactamente 8 equipos.');
            }
        });
    })();
    </script>
    <?php } ?>

    <?php
    $phasesKnock = [
        Quiniela::F_DIECISEISAVOS => ['max' => 16, 'poolKey' => 'dieciseisavos'],
        Quiniela::F_OCTAVOS => ['max' => 8, 'poolKey' => 'octavos'],
        Quiniela::F_CUARTOS => ['max' => 4, 'poolKey' => 'cuartos'],
        Quiniela::F_SEMIFINAL => ['max' => 2, 'poolKey' => 'semifinal'],
        Quiniela::F_FINAL => ['max' => 1, 'poolKey' => 'final'],
    ];
    foreach ($phasesKnock as $faseCode => $meta) {
        $pk = $meta['poolKey'];
        $poolRaw = $pools[$pk] ?? null;
        $eqList = quiniela_admin_eq_pool($equiposSelector, is_array($poolRaw) ? $poolRaw : []);
        $sel = $ofIds[$faseCode] ?? [];
        $fid = 'formOf_' . preg_replace('/[^a-z0-9]/', '_', $faseCode);
        ?>
    <h6 class="section-title"><?php echo htmlspecialchars(Quiniela::etiquetaFase($faseCode)); ?></h6>
    <?php if ($poolRaw === null) { ?>
    <p class="text-muted small mb-4">Complete las fases anteriores para habilitar esta sección.</p>
    <?php } else { ?>
    <form method="post" action="<?php echo htmlspecialchars($qBase . '?v_resultados=1'); ?>" class="bg-white rounded shadow-sm p-3 mb-4" id="<?php echo htmlspecialchars($fid); ?>">
        <input type="hidden" name="fase_oficial" value="<?php echo htmlspecialchars($faseCode); ?>">
        <p class="small text-muted">Seleccione exactamente <strong><?php echo (int) $meta['max']; ?></strong> equipo(s).</p>
        <div class="row">
            <?php
            foreach ($eqList as $eq) {
                $eid = (int) $eq['id'];
                $iso = $eq['iso'] ?? quiniela_paises_iso_por_nombre((string) $eq['nombre']);
                $chk = in_array($eid, $sel, true);
                if ($faseCode === Quiniela::F_FINAL) {
                    ?>
            <div class="col-12 mb-2">
                <label class="form-check d-flex align-items-center">
                    <input type="radio" class="form-check-input" name="equipo_oficial_rad" value="<?php echo $eid; ?>"<?php echo $chk ? ' checked' : ''; ?> required>
                    <span class="ms-2"><?php echo quiniela_flag_icon_html($iso, (string) $eq['nombre'], true); ?></span>
                </label>
            </div>
                    <?php
                } else {
                    ?>
            <div class="col-6 col-md-4 col-lg-3 mb-2">
                <label class="form-check d-flex align-items-center">
                    <input type="checkbox" class="form-check-input js-of-kn" name="equipo_oficial[]" value="<?php echo $eid; ?>"<?php echo $chk ? ' checked' : ''; ?>>
                    <span class="ms-2"><?php echo quiniela_flag_icon_html($iso, (string) $eq['nombre'], true); ?></span>
                </label>
            </div>
                    <?php
                }
            }
            ?>
        </div>
        <button type="submit" name="guardar_oficial_fase" value="1" class="btn btn-warning btn-sm mt-2">Guardar</button>
    </form>
        <?php
        $maxJs = (int) $meta['max'];
        if ($faseCode !== Quiniela::F_FINAL) {
            ?>
    <script>
    (function () {
        var form = document.getElementById('<?php echo htmlspecialchars($fid); ?>');
        if (!form) return;
        var max = <?php echo $maxJs; ?>;
        form.querySelectorAll('.js-of-kn').forEach(function (cb) {
            cb.addEventListener('change', function () {
                var n = form.querySelectorAll('.js-of-kn:checked').length;
                if (n > max) { cb.checked = false; alert('Máximo ' + max + ' equipos.'); }
            });
        });
        form.addEventListener('submit', function (e) {
            if (form.querySelectorAll('.js-of-kn:checked').length !== max) {
                e.preventDefault();
                alert('Debe elegir exactamente ' + max + ' equipo(s).');
            }
        });
    })();
    </script>
            <?php
        }
    }
    }
    ?>
</div>

<script>
document.querySelectorAll('.js-of-grupo').forEach(function (cb) {
    cb.addEventListener('change', function () {
        var g = cb.getAttribute('data-grupo');
        var boxes = document.querySelectorAll('.js-of-grupo[data-grupo="' + g + '"]');
        var n = 0;
        boxes.forEach(function (b) { if (b.checked) n++; });
        if (n > 2) { cb.checked = false; alert('Solo 2 equipos por grupo.'); }
    });
});
(function () {
    var form = document.getElementById('formOfGruposAdmin');
    if (!form) return;
    form.addEventListener('submit', function (e) {
        var map = {};
        form.querySelectorAll('.js-of-grupo').forEach(function (cb) {
            var g = cb.getAttribute('data-grupo');
            if (!map[g]) map[g] = 0;
            if (cb.checked) map[g]++;
        });
        for (var k in map) {
            if (map[k] !== 2) {
                e.preventDefault();
                alert('Debe elegir exactamente 2 clasificados por cada grupo.');
                return;
            }
        }
    });
})();
</script>
<?php include __DIR__ . '/footer.php'; ?>
