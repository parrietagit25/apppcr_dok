<?php
if (!isset($_SESSION['code'])) {
    header('Location: salir.php');
    exit();
}
include __DIR__ . '/header.php';
include __DIR__ . '/quiniela_include_banderas.php';
$qBase = rtrim(BASE_URL_CONTROLLER, '/') . '/QuinielaController.php';

$ordenFases = Quiniela::ordenFases();

$poolIds = is_array($poolDisponibleArma ?? null) ? $poolDisponibleArma : [];
$equiposEnPool = [];
if ($poolIds !== []) {
    $flip = array_flip(array_map('intval', $poolIds));
    foreach ($equiposSelector as $eq) {
        if (isset($flip[(int) $eq['id']])) {
            $equiposEnPool[] = $eq;
        }
    }
}

$nGrupos = count($gruposAdmin ?? []);
$mapSelGrupos = $mapSelGruposUsuario ?? [];
$idxActual = array_search($faseActualUsuario, $ordenFases, true);
if ($idxActual === false) {
    $idxActual = 0;
}

/** @var array $resumenQuinielaUsuario */
$resumen = $resumenQuinielaUsuario ?? ['fases' => [], 'cerrada' => false];
$fasesResumen = $resumen['fases'] ?? [];

function quiniela_arma_fase_completada_en_resumen(string $fase, array $fasesResumen): bool
{
    foreach ($fasesResumen as $bloque) {
        if (($bloque['fase'] ?? '') !== $fase) {
            continue;
        }
        if ($fase === Quiniela::F_GRUPOS) {
            $grs = $bloque['grupos'] ?? [];
            $totalEq = 0;
            foreach ($grs as $g) {
                if (count($g['equipos'] ?? []) !== 2) {
                    return false;
                }
                $totalEq += 2;
            }
            return $totalEq === 24;
        }
        $n = Quiniela::cuentaEsperadaFase($fase);
        return $n > 0 && count($bloque['equipos'] ?? []) === $n;
    }
    return false;
}
?>
<style>
.quiniela-paso { font-size: 0.75rem; white-space: nowrap; }
.quiniela-paso.activa { font-weight: 700; color: #0d6efd; }
.quiniela-paso.hecha { color: #198754; }
.quiniela-check-grid .form-check { min-height: 2.25rem; }
</style>

<div class="container mt-3 mb-5 pb-5">
    <div class="d-flex align-items-center mb-3 flex-wrap gap-2">
        <div class="bg-success text-white px-3 py-2 rounded-start">
            <i class="bi bi-ui-checks-grid"></i> Arma tu quiniela
        </div>
        <div class="bg-white text-muted px-3 py-2 rounded-end flex-grow-1">
            Selecciona tus clasificados por fase hasta elegir campeón
        </div>
        <a href="<?php echo htmlspecialchars($qBase); ?>" class="btn btn-outline-secondary btn-sm">Menú quiniela</a>
    </div>

    <?php if ($mensaje !== '') { ?>
    <div class="alert alert-<?php echo htmlspecialchars($mensajeTipo); ?>"><?php echo htmlspecialchars($mensaje); ?></div>
    <?php } ?>

    <?php if (!empty($quinielaNuevosRegistrosCerrados) || empty($cartaColaborador)) { ?>
    <div class="alert alert-warning">
        <strong>Registro cerrado.</strong> Ya no se admiten nuevas quinielas.
        Solo pueden continuar quienes ya tenían su carta registrada.
    </div>
    <a href="<?php echo htmlspecialchars($qBase); ?>" class="btn btn-outline-secondary btn-sm">Volver al menú</a>
    <?php } else { ?>

    <p class="small text-muted mb-2">
        <strong>Fase actual:</strong> <?php echo htmlspecialchars(Quiniela::etiquetaFase($faseActualUsuario)); ?>
        <?php if ($cartaCerrada) { ?>
        <span class="badge bg-secondary ms-1">Quiniela cerrada</span>
        <?php } ?>
    </p>

    <div class="d-flex flex-wrap gap-1 mb-4 align-items-center">
        <?php
        foreach ($ordenFases as $i => $code) {
            $cls = 'quiniela-paso text-muted';
            if ($code === $faseActualUsuario && !$cartaCerrada) {
                $cls = 'quiniela-paso activa';
            } elseif ($cartaCerrada || ($i < $idxActual) || quiniela_arma_fase_completada_en_resumen($code, $fasesResumen)) {
                $cls = 'quiniela-paso hecha';
            }
            $sep = $i > 0 ? '<span class="text-muted px-1">→</span>' : '';
            echo $sep . '<span class="' . htmlspecialchars($cls) . '">' . htmlspecialchars(Quiniela::etiquetaFase($code)) . '</span>';
        }
        ?>
    </div>

    <?php if ($nGrupos === 0) { ?>
    <div class="alert alert-warning">La quiniela aún no está lista. Espere a que administración cargue los grupos y equipos.</div>
    <?php } elseif ($cartaCerrada) { ?>
    <p class="text-muted small">Su quiniela está cerrada. Solo consulta.</p>
    <?php foreach ($fasesResumen as $bloque) {
        $et = htmlspecialchars($bloque['etiqueta'] ?? '');
        ?>
    <div class="card mb-3 shadow-sm">
        <div class="card-header py-2 fw-bold"><?php echo $et; ?></div>
        <div class="card-body py-2">
            <?php if (!empty($bloque['grupos'])) { ?>
            <div class="row g-2">
                <?php foreach ($bloque['grupos'] as $gr) { ?>
                <div class="col-md-6">
                    <div class="small text-secondary"><?php echo htmlspecialchars($gr['nombre_grupo'] ?? ''); ?></div>
                    <ul class="list-unstyled mb-0 small">
                        <?php foreach ($gr['equipos'] ?? [] as $it) {
                            echo '<li>' . ($it['html'] ?? htmlspecialchars($it['nombre'] ?? '')) . '</li>';
                        } ?>
                    </ul>
                </div>
                <?php } ?>
            </div>
            <?php } else { ?>
            <ul class="list-unstyled mb-0">
                <?php foreach ($bloque['equipos'] ?? [] as $it) {
                    echo '<li>' . ($it['html'] ?? htmlspecialchars($it['nombre'] ?? '')) . '</li>';
                } ?>
            </ul>
            <?php } ?>
        </div>
    </div>
    <?php } ?>

    <?php } else { ?>

    <?php
    foreach ($fasesResumen as $bloque) {
        $fc = $bloque['fase'] ?? '';
        if ($fc === $faseActualUsuario || $fc === '') {
            continue;
        }
        if (!quiniela_arma_fase_completada_en_resumen($fc, $fasesResumen)) {
            continue;
        }
        ?>
    <div class="card mb-3 border-secondary">
        <div class="card-header py-2 bg-light small"><?php echo htmlspecialchars($bloque['etiqueta'] ?? ''); ?> <span class="text-muted">(guardado)</span></div>
        <div class="card-body py-2 small">
            <?php if (!empty($bloque['grupos'])) { ?>
            <div class="row g-2">
                <?php foreach ($bloque['grupos'] as $gr) { ?>
                <div class="col-md-6">
                    <strong><?php echo htmlspecialchars($gr['nombre_grupo'] ?? ''); ?></strong>
                    <ul class="list-unstyled mb-0">
                        <?php foreach ($gr['equipos'] ?? [] as $it) {
                            echo '<li>' . ($it['html'] ?? '') . '</li>';
                        } ?>
                    </ul>
                </div>
                <?php } ?>
            </div>
            <?php } else { ?>
            <ul class="list-unstyled mb-0">
                <?php foreach ($bloque['equipos'] ?? [] as $it) {
                    echo '<li>' . ($it['html'] ?? '') . '</li>';
                } ?>
            </ul>
            <?php } ?>
        </div>
    </div>
    <?php } ?>

    <?php if ($faseActualUsuario === Quiniela::F_GRUPOS) { ?>
    <form method="post" action="<?php echo htmlspecialchars($qBase . '?arma_tu_quiniela=1'); ?>" id="formFaseGrupos">
        <p class="small text-muted">Marque exactamente <strong>2 equipos</strong> por grupo que pasan de ronda.</p>
        <?php foreach ($gruposAdmin as $g) {
            $gid = (int) $g['id'];
            $prev = $mapSelGrupos[$gid] ?? [];
            ?>
        <div class="bg-white rounded shadow-sm p-3 mb-3">
            <div class="fw-bold mb-2"><?php echo htmlspecialchars($g['nombre_grupo']); ?> <span class="text-muted small">(Grupo <?php echo (int) $g['orden_grupo']; ?>)</span></div>
            <div class="row quiniela-check-grid">
                <?php foreach ($g['equipos'] as $eq) {
                    $eid = (int) $eq['id'];
                    $chk = in_array($eid, $prev, true) ? ' checked' : '';
                    ?>
                <div class="col-6 col-md-3">
                    <label class="form-check d-flex align-items-center">
                        <input type="checkbox" class="form-check-input js-grupo-check" name="grupo_<?php echo $gid; ?>[]" value="<?php echo $eid; ?>" data-grupo="<?php echo $gid; ?>"<?php echo $chk; ?>>
                        <span class="ms-2"><?php echo quiniela_flag_icon_html($eq['iso'] ?? null, (string) $eq['nombre'], true); ?></span>
                    </label>
                </div>
                <?php } ?>
            </div>
        </div>
        <?php } ?>
        <button type="submit" name="guardar_fase_grupos" value="1" class="btn btn-success">
            <i class="bi bi-save"></i> Guardar fase
        </button>
    </form>
    <script>
    document.querySelectorAll('.js-grupo-check').forEach(function (cb) {
        cb.addEventListener('change', function () {
            var g = cb.getAttribute('data-grupo');
            var boxes = document.querySelectorAll('.js-grupo-check[data-grupo="' + g + '"]');
            var n = 0;
            boxes.forEach(function (b) { if (b.checked) n++; });
            if (n > 2) {
                cb.checked = false;
                alert('Solo puede elegir 2 equipos por grupo.');
            }
        });
    });
    document.getElementById('formFaseGrupos').addEventListener('submit', function (e) {
        var grupos = {};
        document.querySelectorAll('.js-grupo-check').forEach(function (cb) {
            var g = cb.getAttribute('data-grupo');
            if (!grupos[g]) grupos[g] = 0;
            if (cb.checked) grupos[g]++;
        });
        for (var k in grupos) {
            if (grupos[k] !== 2) {
                e.preventDefault();
                alert('Debe elegir exactamente 2 equipos en cada grupo.');
                return;
            }
        }
    });
    </script>

    <?php } elseif ($poolDisponibleArma === null && $faseActualUsuario === Quiniela::F_DIECISEISAVOS) { ?>
    <div class="alert alert-warning">Complete primero la fase de grupos y los 8 mejores terceros para continuar.</div>

    <?php } elseif (in_array($faseActualUsuario, [
        Quiniela::F_MEJORES_TERCEROS,
        Quiniela::F_DIECISEISAVOS,
        Quiniela::F_OCTAVOS,
        Quiniela::F_CUARTOS,
        Quiniela::F_SEMIFINAL,
        Quiniela::F_FINAL,
    ], true)) {
        $max = Quiniela::cuentaEsperadaFase($faseActualUsuario);
        $prevSel = $idsSeleccionFasePantalla ?? [];
        ?>
    <form method="post" action="<?php echo htmlspecialchars($qBase . '?arma_tu_quiniela=1'); ?>" id="formFaseSel" class="bg-white rounded shadow-sm p-3">
        <input type="hidden" name="fase_guardar" value="<?php echo htmlspecialchars($faseActualUsuario); ?>">
        <p class="small text-muted mb-3">
            Seleccione exactamente <strong><?php echo (int) $max; ?></strong>
            equipo<?php echo $max === 1 ? '' : 's'; ?>.
            <?php if ($faseActualUsuario === Quiniela::F_FINAL) { ?>
            El equipo elegido será su campeón y se cerrará la quiniela.
            <?php } ?>
        </p>
        <div class="row quiniela-check-grid">
            <?php
            foreach ($equiposEnPool as $eq) {
                $eid = (int) $eq['id'];
                $iso = $eq['iso'] ?? quiniela_paises_iso_por_nombre((string) $eq['nombre']);
                $chk = in_array($eid, $prevSel, true);
                if ($faseActualUsuario === Quiniela::F_FINAL) {
                    ?>
            <div class="col-12 mb-2">
                <label class="form-check d-flex align-items-center">
                    <input type="radio" class="form-check-input js-fase-sel" name="equipo_sel" value="<?php echo $eid; ?>"<?php echo $chk ? ' checked' : ''; ?> required>
                    <span class="ms-2"><?php echo quiniela_flag_icon_html($iso, (string) $eq['nombre'], true); ?></span>
                </label>
            </div>
                    <?php
                } else {
                    ?>
            <div class="col-6 col-md-4 col-lg-3 mb-2">
                <label class="form-check d-flex align-items-center">
                    <input type="checkbox" class="form-check-input js-fase-sel" name="equipo_sel[]" value="<?php echo $eid; ?>"<?php echo $chk ? ' checked' : ''; ?>>
                    <span class="ms-2"><?php echo quiniela_flag_icon_html($iso, (string) $eq['nombre'], true); ?></span>
                </label>
            </div>
                    <?php
                }
            }
            ?>
        </div>
        <?php if (count($equiposEnPool) === 0) { ?>
        <div class="alert alert-danger mt-2">No hay equipos disponibles para esta fase. Revise que guardó las fases anteriores.</div>
        <?php } else { ?>
        <button type="submit" name="guardar_fase_seleccion" value="1" class="btn btn-success mt-3">
            <i class="bi bi-save"></i> Guardar fase
        </button>
        <?php } ?>
    </form>
    <script>
    (function () {
        var max = <?php echo (int) $max; ?>;
        var form = document.getElementById('formFaseSel');
        if (!form) return;
        if (max > 1) {
            form.querySelectorAll('.js-fase-sel').forEach(function (cb) {
                cb.addEventListener('change', function () {
                    var boxes = form.querySelectorAll('.js-fase-sel:checked');
                    if (boxes.length > max) {
                        cb.checked = false;
                        alert('Máximo ' + max + ' equipos.');
                    }
                });
            });
        }
        form.addEventListener('submit', function (e) {
            var boxes = form.querySelectorAll('.js-fase-sel:checked');
            if (boxes.length !== max) {
                e.preventDefault();
                alert('Debe elegir exactamente ' + max + ' equipo(s).');
                return;
            }
            <?php if ($faseActualUsuario === Quiniela::F_FINAL) { ?>
            if (!confirm('¿Confirmar campeón? La quiniela quedará cerrada.')) e.preventDefault();
            <?php } ?>
        });
    })();
    </script>
    <?php } else { ?>
    <div class="alert alert-info">No hay acciones disponibles para esta fase.</div>
    <?php } ?>

    <?php } ?>
    <?php } ?>
</div>

<?php include __DIR__ . '/footer.php'; ?>
