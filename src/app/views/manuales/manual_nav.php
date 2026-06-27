<?php
/** @var string $manual_activo colaborador|supervisor|mantenimiento */
$puede_supervisor = $puede_manual_supervisor ?? false;
$puede_mantenimiento = $puede_manual_mantenimiento ?? false;
$es_admin = ((int) ($tipo_usuario ?? 0)) === 1;
$base = BASE_URL_CONTROLLER . '/MainController.php';
?>
<ul class="nav nav-pills manual-nav-pills flex-wrap gap-1 mb-3">
    <li class="nav-item">
        <a class="nav-link <?php echo ($manual_activo ?? '') === 'colaborador' ? 'active' : ''; ?>"
           href="<?php echo htmlspecialchars($base . '?manual_colaborador=1'); ?>">
            <i class="bi bi-person"></i> Manual Colaborador
        </a>
    </li>
    <?php if ($puede_supervisor || $es_admin) { ?>
    <li class="nav-item">
        <a class="nav-link <?php echo ($manual_activo ?? '') === 'supervisor' ? 'active' : ''; ?>"
           href="<?php echo htmlspecialchars($base . '?manual_supervisor=1'); ?>">
            <i class="bi bi-person-check"></i> Manual Supervisor
        </a>
    </li>
    <?php } ?>
    <?php if ($puede_mantenimiento || $es_admin) { ?>
    <li class="nav-item">
        <a class="nav-link <?php echo ($manual_activo ?? '') === 'mantenimiento' ? 'active' : ''; ?>"
           href="<?php echo htmlspecialchars($base . '?manual_mantenimiento=1'); ?>">
            <i class="bi bi-tools"></i> Manual Mantenimiento
        </a>
    </li>
    <?php } ?>
</ul>
