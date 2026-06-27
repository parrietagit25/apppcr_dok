<?php
if (!isset($_SESSION['code'])) {
    header('Location: salir.php');
    exit();
}
include __DIR__ . '/../header.php';
include __DIR__ . '/manual_styles.php';
$manual_activo = 'mantenimiento';
$mainUrl = BASE_URL_CONTROLLER . '/MainController.php';
?>
<div class="container mt-4 mb-5 pb-5">
    <?php include __DIR__ . '/manual_nav.php'; ?>

    <div class="manual-hero">
        <h4 class="mb-1"><i class="bi bi-tools"></i> Manual de Mantenimiento</h4>
        <p class="mb-0 small opacity-75">Administración de usuarios, encargados y reportes — zona RRHH</p>
    </div>

    <div class="alert alert-warning">
        <i class="bi bi-lock"></i>
        Esta zona solo está disponible para <strong>RRHH</strong>, usuarios de mantenimiento (tipo 5) y
        <strong>administrador</strong> (tipo 1). Acceso:
        <a href="<?php echo htmlspecialchars($mainUrl . '?mantenimineto=1'); ?>">Mantenimiento</a>
    </div>

    <div class="card manual-section">
        <div class="card-body">
            <h5 class="fw-bold"><i class="bi bi-grid text-primary"></i> 1. Módulos de mantenimiento</h5>
            <div class="table-responsive">
                <table class="table table-sm table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Módulo</th>
                            <th>Función</th>
                            <th>Quién accede</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Usuarios</strong></td>
                            <td>Cambiar contraseña, activar/desactivar login, estatus en planilla.</td>
                            <td>RRHH, tipo 5</td>
                        </tr>
                        <tr>
                            <td><strong>Encargados</strong></td>
                            <td>Asignar rol supervisor (tipo 6) y personal a cargo.</td>
                            <td>RRHH, tipo 5</td>
                        </tr>
                        <tr>
                            <td><strong>R-Encargados</strong></td>
                            <td>Gestión avanzada de supervisores y asignaciones.</td>
                            <td>Solo admin (tipo 1)</td>
                        </tr>
                        <tr>
                            <td><strong>Usuarios No listados</strong></td>
                            <td>Crear colaboradores fuera de planilla (código externo + acceso).</td>
                            <td>RRHH, tipo 5</td>
                        </tr>
                        <tr>
                            <td><strong>Permisos</strong></td>
                            <td>Reporte / exportación de solicitudes registradas.</td>
                            <td>RRHH, tipo 5</td>
                        </tr>
                        <tr>
                            <td><strong>Vacaciones</strong></td>
                            <td>Reporte de solicitudes de vacaciones.</td>
                            <td>RRHH, tipo 5</td>
                        </tr>
                        <tr>
                            <td><strong>Mant Cumple</strong></td>
                            <td>Ocultar o mostrar colaboradores en la lista pública de cumpleaños.</td>
                            <td>RRHH, tipo 5</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card manual-section">
        <div class="card-body">
            <h5 class="fw-bold"><i class="bi bi-person-gear text-primary"></i> 2. Usuarios</h5>
            <div class="manual-step">
                <strong>Activar / desactivar acceso</strong>
                <p class="mb-0">Cambie el estado del usuario en <em>empleado_log</em> (activo/inactivo) para permitir o bloquear el login sin borrar historial.</p>
            </div>
            <div class="manual-step">
                <strong>Restablecer contraseña</strong>
                <p class="mb-0">Seleccione el colaborador e ingrese la nueva contraseña. Comuníquela por canal seguro.</p>
            </div>
            <div class="manual-step">
                <strong>Estatus en planilla</strong>
                <p class="mb-0">Actualice estatus del empleado (A, V, I, etc.) cuando corresponda según Payday/planilla.</p>
            </div>
        </div>
    </div>

    <div class="card manual-section">
        <div class="card-body">
            <h5 class="fw-bold"><i class="bi bi-diagram-3 text-primary"></i> 3. Encargados y supervisores</h5>
            <div class="manual-step">
                <strong>Asignar supervisor (tipo 6)</strong>
                <ol class="mb-0 mt-2">
                    <li>Busque al colaborador por código.</li>
                    <li>Asigne como encargado/supervisor.</li>
                    <li>El usuario verá <em>Administrar Permisos</em> y <em>Mi Personal</em> en Mi Espacio.</li>
                </ol>
            </div>
            <div class="manual-step">
                <strong>Personal a cargo</strong>
                <p class="mb-0">En Encargados o R-Encargados, vincule qué colaboradores reportan a cada supervisor. Sin esta asignación, el colaborador no podrá seleccionarlo al solicitar permisos.</p>
            </div>
            <div class="manual-step">
                <strong>Quitar rol de supervisor</strong>
                <p class="mb-0">Revise que no queden solicitudes críticas pendientes antes de bajar el tipo de usuario a colaborador (tipo 2).</p>
            </div>
        </div>
    </div>

    <div class="card manual-section">
        <div class="card-body">
            <h5 class="fw-bold"><i class="bi bi-person-x text-primary"></i> 4. Usuarios no listados</h5>
            <p>Use este módulo cuando el colaborador <strong>no aparece en el pase diario de planilla</strong> pero necesita acceso (externos, casos especiales).</p>
            <ol>
                <li>Registre código, nombre, apellido, fecha de nacimiento y contraseña.</li>
                <li>Asigne tipo de usuario si aplica (colaborador o supervisor).</li>
                <li>Cuando la persona entre a planilla con código real, migre su historial al código definitivo (consulte a soporte técnico si aplica).</li>
            </ol>
        </div>
    </div>

    <div class="card manual-section">
        <div class="card-body">
            <h5 class="fw-bold"><i class="bi bi-balloon text-primary"></i> 5. Mant Cumple</h5>
            <p>Permite quitar temporalmente a un colaborador de la pantalla pública de cumpleaños sin modificar su fecha de nacimiento en planilla.</p>
            <ol class="mb-0">
                <li>Entre a <strong>Mant Cumple</strong>.</li>
                <li>Localice al colaborador del mes.</li>
                <li>Use <em>Quitar de lista</em> u <em>Mostrar en lista</em>.</li>
                <li>Verifique en <a href="<?php echo htmlspecialchars($mainUrl . '?cumple=1'); ?>">Cumpleaños</a>.</li>
            </ol>
        </div>
    </div>

    <div class="card manual-section">
        <div class="card-body">
            <h5 class="fw-bold"><i class="bi bi-file-earmark-spreadsheet text-primary"></i> 6. Reportes Permisos y Vacaciones</h5>
            <p class="mb-0">Consulte solicitudes históricas y exporte a Excel cuando necesite auditoría o reportes a gerencia. Los permisos activos se gestionan también desde Mi Espacio → V-Permisos (RRHH).</p>
        </div>
    </div>
</div>

<nav class="navbar fixed-bottom navbar-light bg-light border-top">
    <div class="container-fluid">
        <a href="<?php echo htmlspecialchars($mainUrl . '?mantenimineto=1'); ?>" class="navbar-brand text-center w-100">VOLVER A MANTENIMIENTO</a>
    </div>
</nav>

<?php include __DIR__ . '/../footer.php'; ?>
