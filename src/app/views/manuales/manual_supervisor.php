<?php
if (!isset($_SESSION['code'])) {
    header('Location: salir.php');
    exit();
}
include __DIR__ . '/../header.php';
include __DIR__ . '/manual_styles.php';
$manual_activo = 'supervisor';
$rrhhUrl = BASE_URL_CONTROLLER . '/RRHHController.php';
?>
<div class="container mt-4 mb-5 pb-5">
    <?php include __DIR__ . '/manual_nav.php'; ?>

    <div class="manual-hero">
        <h4 class="mb-1"><i class="bi bi-person-check"></i> Manual del Supervisor</h4>
        <p class="mb-0 small opacity-75">Aprobación de permisos y gestión de personal a cargo</p>
    </div>

    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i>
        Este manual es para <strong>supervisores / jefes</strong> (tipo 6) y para el equipo de <strong>RRHH</strong>.
        Si un colaborador no ve las opciones descritas, debe solicitar a RRHH la activación de su perfil de supervisor.
    </div>

    <div class="card manual-section">
        <div class="card-body">
            <h5 class="fw-bold"><i class="bi bi-shield-check text-primary"></i> 1. Su rol como supervisor</h5>
            <p>Como supervisor usted puede:</p>
            <ul>
                <li>Recibir y <strong>aprobar o declinar</strong> solicitudes de permiso de su personal.</li>
                <li>Consultar y gestionar el listado de <strong>colaboradores a su cargo</strong>.</li>
                <li>Actuar como jefe seleccionado cuando un colaborador envía permiso o vacaciones.</li>
            </ul>
            <p class="mb-0">Acceda desde <a href="<?php echo htmlspecialchars($rrhhUrl); ?>">Mi Espacio</a>.</p>
        </div>
    </div>

    <div class="card manual-section">
        <div class="card-body">
            <h5 class="fw-bold"><i class="bi bi-check2-square text-primary"></i> 2. Administrar permisos</h5>
            <p>Ruta: Mi Espacio → <strong>Administrar Permisos</strong></p>
            <div class="manual-step">
                <strong>Pasos para revisar solicitudes</strong>
                <ol class="mb-0 mt-2">
                    <li>Entre a <em>Administrar Permisos</em>.</li>
                    <li>Verá la tabla con solicitudes de su personal (nombre, tipo, fecha, estado).</li>
                    <li>Haga clic en una fila o botón de detalle para ver fechas, motivo y archivo adjunto.</li>
                    <li>Revise que el tipo de licencia y las fechas sean correctas.</li>
                    <li>Elija <strong>Aprobar</strong> o <strong>Declinar</strong>.</li>
                </ol>
            </div>
            <div class="manual-step">
                <strong>Estados de una solicitud</strong>
                <ul class="mb-0">
                    <li><span class="badge bg-warning text-dark">Solicitado</span> — pendiente de su acción.</li>
                    <li><span class="badge bg-success">Aprobado</span> — usted aprobó; RRHH puede completar el proceso.</li>
                    <li><span class="badge bg-danger">Declinado</span> — rechazada por usted.</li>
                </ul>
            </div>
            <div class="manual-step">
                <strong>Buenas prácticas</strong>
                <ul class="mb-0">
                    <li>Responda en un plazo razonable para no retrasar al colaborador.</li>
                    <li>Verifique días de vacaciones cuando el permiso sea tipo <em>Vacaciones</em>.</li>
                    <li>Si la solicitud no le corresponde, contacte a RRHH para reasignar supervisores.</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="card manual-section">
        <div class="card-body">
            <h5 class="fw-bold"><i class="bi bi-people text-primary"></i> 3. Mi Personal</h5>
            <p>Ruta: Mi Espacio → <strong>Mi Personal</strong> (visible para supervisores y gerentes autorizados)</p>
            <div class="manual-step">
                <strong>Qué puede hacer aquí</strong>
                <ul class="mb-0">
                    <li>Ver la lista de colaboradores asignados a su cargo.</li>
                    <li>Consultar quién le reporta directamente en la app.</li>
                </ul>
                <p class="small text-muted mb-0 mt-2">
                    La asignación de personal la realiza RRHH desde Mantenimiento (Encargados / R-Encargados).
                    Si falta alguien en su lista, escriba a RRHH.
                </p>
            </div>
        </div>
    </div>

    <div class="card manual-section">
        <div class="card-body">
            <h5 class="fw-bold"><i class="bi bi-envelope text-primary"></i> 4. Notificaciones</h5>
            <p class="mb-0">
                Al registrarse una solicitud donde usted es jefe, puede recibir notificación por correo
                (según configuración). Revise también la app con frecuencia para no dejar solicitudes pendientes.
            </p>
        </div>
    </div>

    <div class="card manual-section">
        <div class="card-body">
            <h5 class="fw-bold"><i class="bi bi-question-circle text-primary"></i> 5. Soporte</h5>
            <p class="mb-0">
                Dudas sobre permisos, personal a cargo o acceso de supervisor:
                <a href="mailto:rrhh@grupopcr.com.pa">rrhh@grupopcr.com.pa</a>
            </p>
        </div>
    </div>
</div>

<nav class="navbar fixed-bottom navbar-light bg-light border-top">
    <div class="container-fluid">
        <a href="<?php echo BASE_URL_CONTROLLER; ?>/MainController.php" class="navbar-brand text-center w-100">VOLVER AL INICIO</a>
    </div>
</nav>

<?php include __DIR__ . '/../footer.php'; ?>
