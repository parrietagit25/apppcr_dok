<?php
if (!isset($_SESSION['code'])) {
    header('Location: salir.php');
    exit();
}
include __DIR__ . '/../header.php';
include __DIR__ . '/manual_styles.php';
$manual_activo = 'colaborador';
$regUrl = BASE_URL_CONTROLLER . '/RegcolaController.php';
$rrhhUrl = BASE_URL_CONTROLLER . '/RRHHController.php';
?>
<div class="container mt-4 mb-5 pb-5">
    <?php include __DIR__ . '/manual_nav.php'; ?>

    <div class="manual-hero">
        <h4 class="mb-1"><i class="bi bi-book"></i> Manual del Colaborador</h4>
        <p class="mb-0 small opacity-75">Registro, acceso y solicitudes en APP PCR</p>
    </div>

    <div class="card manual-section" id="registro">
        <div class="card-body">
            <h5 class="fw-bold"><i class="bi bi-person-plus text-primary"></i> 1. Registro e ingreso</h5>
            <div class="manual-step">
                <strong>Registrarse por primera vez</strong>
                <ol class="mb-0 mt-2">
                    <li>En la pantalla de login, pulse <strong>Registrar Colaborador</strong>.</li>
                    <li>Ingrese su <strong>código de colaborador</strong> (el de planilla).</li>
                    <li>El sistema validará que exista en planilla y que no esté registrado aún.</li>
                    <li>Complete la contraseña y confirme el registro.</li>
                </ol>
                <p class="small text-muted mb-0 mt-2">
                    Enlace directo:
                    <a href="<?php echo htmlspecialchars($regUrl . '?reg_col=1'); ?>">Registro de colaborador</a>
                </p>
            </div>
            <div class="manual-step">
                <strong>Iniciar sesión</strong>
                <p class="mb-0">Use su código de colaborador y contraseña en la pantalla principal de acceso.</p>
            </div>
            <div class="manual-step">
                <strong>Recuperar contraseña</strong>
                <ol class="mb-0 mt-2">
                    <li>En login, elija <strong>Recuperar Contraseña</strong>.</li>
                    <li>Ingrese su código; recibirá un enlace al correo registrado.</li>
                </ol>
            </div>
        </div>
    </div>

    <div class="card manual-section" id="inicio">
        <div class="card-body">
            <h5 class="fw-bold"><i class="bi bi-house text-primary"></i> 2. Página principal (Inicio)</h5>
            <p>Desde el inicio accede a:</p>
            <ul>
                <li><strong>Mi Espacio</strong> — solicitudes y datos personales.</li>
                <li><strong>Mis Beneficios</strong> — convenios y descuentos.</li>
                <li><strong>Mi Carnet</strong> — carnet digital.</li>
                <li><strong>Cumpleaños</strong> — cumpleañeros del mes.</li>
                <li><strong>Mi Póliza</strong> — instructivos de seguro.</li>
                <li><strong>Correo / Línea de apoyo</strong> — contacto con RRHH.</li>
            </ul>
        </div>
    </div>

    <div class="card manual-section" id="mi-espacio">
        <div class="card-body">
            <h5 class="fw-bold"><i class="bi bi-person-badge text-primary"></i> 3. Mi Espacio — Información personal</h5>
            <p>Ruta: <a href="<?php echo htmlspecialchars($rrhhUrl); ?>">Mi Espacio</a></p>

            <div class="manual-step">
                <strong>Mis Datos</strong>
                <p class="mb-0">Consulte su información personal y laboral. Puede solicitar actualización de datos de contacto.</p>
            </div>
            <div class="manual-step">
                <strong>Solicitar Carta de trabajo</strong>
                <ol class="mb-0 mt-2">
                    <li>Entre a <em>Solicitar Carta</em>.</li>
                    <li>Indique el motivo o tipo de carta.</li>
                    <li>Adjunte documentos si aplica y envíe.</li>
                    <li>RRHH procesará la solicitud y le notificará.</li>
                </ol>
            </div>
            <div class="manual-step">
                <strong>Solicitar Calamidad</strong>
                <p class="mb-0">Describa la situación, adjunte respaldos y envíe. RRHH revisará el caso.</p>
            </div>
            <div class="manual-step">
                <strong>Solicitar Uniforme</strong>
                <p class="mb-0">Seleccione tipo, talla y cantidad. La entrega depende de disponibilidad.</p>
            </div>
        </div>
    </div>

    <div class="card manual-section" id="ausencias">
        <div class="card-body">
            <h5 class="fw-bold"><i class="bi bi-calendar-check text-primary"></i> 4. Permisos, vacaciones e incapacidades</h5>

            <div class="manual-step">
                <strong>Solicitar Permiso</strong>
                <ol class="mb-0 mt-2">
                    <li>Mi Espacio → <em>Solicitar Permiso</em>.</li>
                    <li>Elija el <strong>tipo de licencia</strong> (enfermedad, duelo, cita médica, compensatorio, etc.).</li>
                    <li>Indique <strong>fecha inicio y fin</strong>.</li>
                    <li>Seleccione su <strong>supervisor</strong> en la lista (debe tener supervisores asignados).</li>
                    <li>Adjunte archivo si corresponde y envíe.</li>
                </ol>
                <p class="small text-muted mb-0 mt-2">Estados: <em>Solicitado</em> → su supervisor aprueba o declina → RRHH puede validar según el tipo.</p>
            </div>

            <div class="manual-step">
                <strong>Mis Vacaciones</strong>
                <ol class="mb-0 mt-2">
                    <li>Consulte días acumulados en <em>Mis Vacaciones</em>.</li>
                    <li>Solicite el rango de fechas y seleccione supervisor.</li>
                    <li>Revise el historial y estado de cada solicitud.</li>
                </ol>
            </div>

            <div class="manual-step">
                <strong>Mis Incapacidades</strong>
                <ol class="mb-0 mt-2">
                    <li>Entre a <em>Mis Incapacidades</em>.</li>
                    <li>Registre la incapacidad con fechas e información requerida.</li>
                    <li><strong>Adjunte el documento médico</strong> (obligatorio en la mayoría de casos).</li>
                    <li>Consulte el historial y seguimiento.</li>
                </ol>
            </div>
        </div>
    </div>

    <div class="card manual-section" id="consejos">
        <div class="card-body">
            <h5 class="fw-bold"><i class="bi bi-lightbulb text-primary"></i> 5. Recomendaciones</h5>
            <ul class="mb-0">
                <li>Solicite permisos y vacaciones con <strong>anticipación</strong>.</li>
                <li>Verifique que su supervisor aparezca en el selector antes de enviar.</li>
                <li>Adjunte siempre los documentos de respaldo cuando sean requeridos.</li>
                <li>Si no tiene supervisores asignados, contacte a RRHH:
                    <a href="mailto:rrhh@grupopcr.com.pa">rrhh@grupopcr.com.pa</a>
                </li>
            </ul>
        </div>
    </div>
</div>

<nav class="navbar fixed-bottom navbar-light bg-light border-top">
    <div class="container-fluid">
        <a href="<?php echo BASE_URL_CONTROLLER; ?>/MainController.php" class="navbar-brand text-center w-100">VOLVER AL INICIO</a>
    </div>
</nav>

<?php include __DIR__ . '/../footer.php'; ?>
