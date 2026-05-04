<?php
if (!isset($_SESSION['code'])) {
    header('Location: salir.php');
    exit();
}
include __DIR__ . '/header.php';
include __DIR__ . '/quiniela_include_banderas.php';
$qBase = rtrim(BASE_URL_CONTROLLER, '/') . '/QuinielaController.php';
$res = $resumenOficialPublico['fases'] ?? [];
?>
<div class="container mt-3 mb-5 pb-5">
    <div class="d-flex align-items-center mb-3 flex-wrap gap-2">
        <div class="bg-primary text-white px-3 py-2 rounded-start">
            <i class="bi bi-list-ol"></i> Resultados
        </div>
        <div class="bg-white text-muted px-3 py-2 rounded-end flex-grow-1">
            Clasificados oficiales y campeón
        </div>
        <a href="<?php echo htmlspecialchars($qBase); ?>" class="btn btn-outline-secondary btn-sm">Menú quiniela</a>
    </div>

    <?php foreach ($res as $bloque) {
        $et = htmlspecialchars($bloque['etiqueta'] ?? '');
        $def = !empty($bloque['definida']);
        ?>
    <div class="card mb-3 shadow-sm">
        <div class="card-header py-2 d-flex justify-content-between align-items-center">
            <span class="fw-bold"><?php echo $et; ?></span>
            <?php if (!$def) { ?>
            <span class="badge bg-secondary">Pendiente</span>
            <?php } ?>
        </div>
        <div class="card-body py-3">
            <?php if (!$def) { ?>
            <p class="text-muted small mb-0">Aún no hay datos oficiales para esta fase.</p>
            <?php } elseif (!empty($bloque['grupos'])) { ?>
            <div class="row g-3">
                <?php foreach ($bloque['grupos'] as $gr) { ?>
                <div class="col-md-6">
                    <div class="small text-secondary mb-1"><?php echo htmlspecialchars($gr['nombre_grupo'] ?? ''); ?></div>
                    <ul class="list-unstyled mb-0">
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
</div>

<?php include __DIR__ . '/footer.php'; ?>
