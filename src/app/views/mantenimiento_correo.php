<?php
if (!isset($_SESSION['code'])) {
    header('Location: salir.php');
    exit();
}

include __DIR__ . '/header.php';
?>

<div class="container mt-4 mb-5 pb-5">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <h5 class="fw-bold mb-1">Test de correo (Resend)</h5>
            <p class="text-muted small mb-0">
                Envía un correo de prueba para verificar que Resend está configurado y operativo.
            </p>
        </div>
        <a href="<?php echo BASE_URL_CONTROLLER; ?>/MainController.php?mantenimineto=1" class="btn btn-outline-secondary btn-sm">
            Volver a Mantenimiento
        </a>
    </div>

    <?php if (!empty($mensaje_correo)) { ?>
        <div class="alert alert-<?php echo htmlspecialchars($mensaje_correo_tipo ?? 'info'); ?>">
            <?php echo htmlspecialchars($mensaje_correo); ?>
        </div>
    <?php } ?>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light fw-semibold">Configuración actual</div>
        <div class="card-body">
            <ul class="mb-0">
                <li>
                    <strong>API Key:</strong>
                    <?php if (!empty($resend_api_configurada)) { ?>
                        <span class="text-success">Configurada</span>
                    <?php } else { ?>
                        <span class="text-danger">No configurada</span>
                        — verifique <code>RESEND_API_KEY</code> en el <code>.env</code> (raíz del proyecto o <code>src/.env</code>)
                        y reinicie el contenedor: <code>docker compose up -d</code>
                    <?php } ?>
                </li>
                <li><strong>Remitente:</strong> <?php echo htmlspecialchars($resend_from_name); ?> &lt;<?php echo htmlspecialchars($resend_from_email); ?>&gt;</li>
                <li>
                    <strong>Archivo .env en contenedor:</strong>
                    <?php if (!empty($env_archivo_existe)) { ?>
                        <span class="text-success">Encontrado</span>
                        <code class="small"><?php echo htmlspecialchars($env_archivo_ruta ?? ''); ?></code>
                    <?php } else { ?>
                        <span class="text-danger">No encontrado</span>
                        — monte <code>./.env:/var/www/html/.env</code> y ejecute <code>docker compose up -d --build</code>
                    <?php } ?>
                </li>
                <li>
                    <strong>Variable en entorno Apache:</strong>
                    <?php echo !empty($env_getenv_activo) ? '<span class="text-success">Sí</span>' : '<span class="text-warning">No</span> (se usa lectura directa del .env)'; ?>
                </li>
            </ul>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="<?php echo BASE_URL_CONTROLLER; ?>/MainController.php?mantenimiento_correo=1">
                <div class="mb-3">
                    <label for="email_destino" class="form-label">Correo destino</label>
                    <input
                        type="email"
                        class="form-control"
                        id="email_destino"
                        name="email_destino"
                        required
                        placeholder="correo@ejemplo.com"
                        value="<?php echo htmlspecialchars($email_destino_default ?? ''); ?>"
                    >
                    <div class="form-text">Por defecto se sugiere el correo registrado de su usuario en planilla.</div>
                </div>
                <button type="submit" name="enviar_prueba_correo" value="1" class="btn btn-primary" <?php echo empty($resend_api_configurada) ? 'disabled' : ''; ?>>
                    <i class="bi bi-envelope-check"></i> Enviar correo de prueba
                </button>
            </form>
        </div>
    </div>
</div>

<nav class="navbar fixed-bottom navbar-light bg-light border-top">
    <div class="container-fluid">
        <a href="<?php echo BASE_URL_CONTROLLER; ?>/MainController.php" class="navbar-brand text-center" style="width: 25%;">INICIO</a>
        <a href="#" class="navbar-brand text-center" style="width: 25%;"></a>
        <a href="<?php echo BASE_URL_CONTROLLER; ?>/MainController.php?mantenimineto=1" class="navbar-brand text-center" style="width: 25%;">VOLVER</a>
        <a href="#" class="navbar-brand text-center" style="width: 25%;"></a>
    </div>
</nav>

<?php include __DIR__ . '/footer.php'; ?>
