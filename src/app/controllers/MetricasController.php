<?php 

session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Rrhh.php';

$pdo = Database::connect();
$userModel = new User($pdo);
$pdo_rrhh = Database::connect();
$class_rrhh = new Rrhh($pdo_rrhh);

/* nombre de usuario */
$nombre = $userModel->nombre_colaborador();
$tipo_usuario = $userModel->get_tyte_user();

if (!isset($_SESSION['code'])) {
    header("Location: salir.php");
    exit();
}

$tipo_usuario = $userModel->get_tyte_user();

if (isset($_GET['metricas'])) {

    $cartas_trabajos = $class_rrhh->count_cartas_trabajo();

    $calamidades = $class_rrhh->count_calamidades();

    $permisos = $class_rrhh->count_permisos();

    $incapacidades = $class_rrhh->count_incapacidades();

    $vacaciones = $class_rrhh->count_type_permiso('Vacaciones');

    $compensatorio = $class_rrhh->count_type_permiso('Compensatorio');

    $cita_medica = $class_rrhh->count_type_permiso('Cita Medica');

    $enfermedad = $class_rrhh->count_type_permiso('Enfermedad');

    $tele_trabajo = $class_rrhh->count_type_permiso('Teletrabajo');

    $duelo = $class_rrhh->count_type_permiso('Duelo');

    $tiempo_sin_pago = $class_rrhh->count_type_permiso('Tiempo Sin Pago');

    $permisosChartData = [
        'Vacaciones'        => $class_rrhh->count_permisos_by_type('Vacaciones'),
        'Compensatorios'    => $class_rrhh->count_permisos_by_type('Compensatorio'),
        'Citas médicas'     => $class_rrhh->count_permisos_by_type('Cita Medica'),
        'Enfermedad'        => $class_rrhh->count_permisos_by_type('Enfermedad'),
        'Teletrabajo'       => $class_rrhh->count_permisos_by_type('Teletrabajo'),
        'Duelo'             => $class_rrhh->count_permisos_by_type('Duelo'),
        'Tiempo sin pago'   => $class_rrhh->count_permisos_by_type('Tiempo sin pago'),
    ];

    $dashboardTotals = [
        'Actualización de Datos'   => 41, // 41
        'Cartas de trabajo'        => $class_rrhh->count_cartas_trabajo(), // 54
        'Calamidades'              => $class_rrhh->count_calamidades(),    // 16
        'Permisos solicitados'     => $class_rrhh->count_permisos(), // 123
        'Incapacidades'            => $class_rrhh->count_incapacidades(),     // 53
    ];

    require_once __DIR__ . '/../views/metricas.php';
    exit();
}

if (isset($_GET['organigrama'])) {
    require_once __DIR__ . '/../views/organigrama.php';
    exit();
}

require_once __DIR__ . '/../views/metricas.php';