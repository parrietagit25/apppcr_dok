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

    // Obtener filtros de fecha
    $fecha_desde = isset($_GET['fecha_desde']) && !empty($_GET['fecha_desde']) ? $_GET['fecha_desde'] : null;
    $fecha_hasta = isset($_GET['fecha_hasta']) && !empty($_GET['fecha_hasta']) ? $_GET['fecha_hasta'] : null;

    // Si no hay fechas especificadas, usar el mes actual por defecto
    if (!$fecha_desde && !$fecha_hasta) {
        $fecha_desde = date('Y-m-01'); // Primer día del mes actual
        $fecha_hasta = date('Y-m-d'); // Día actual
    }

    $cartas_trabajos = $class_rrhh->count_cartas_trabajo($fecha_desde, $fecha_hasta);

    $calamidades = $class_rrhh->count_calamidades($fecha_desde, $fecha_hasta);

    $permisos = $class_rrhh->count_permisos($fecha_desde, $fecha_hasta);

    $incapacidades = $class_rrhh->count_incapacidades($fecha_desde, $fecha_hasta);

    $vacaciones = $class_rrhh->count_type_permiso('Vacaciones', $fecha_desde, $fecha_hasta);

    $compensatorio = $class_rrhh->count_type_permiso('Compensatorio', $fecha_desde, $fecha_hasta);

    $cita_medica = $class_rrhh->count_type_permiso('Cita Medica', $fecha_desde, $fecha_hasta);

    $enfermedad = $class_rrhh->count_type_permiso('Enfermedad', $fecha_desde, $fecha_hasta);

    $tele_trabajo = $class_rrhh->count_type_permiso('Teletrabajo', $fecha_desde, $fecha_hasta);

    $duelo = $class_rrhh->count_type_permiso('Duelo', $fecha_desde, $fecha_hasta);

    $tiempo_sin_pago = $class_rrhh->count_type_permiso('Tiempo Sin Pago', $fecha_desde, $fecha_hasta);

    $permisosChartData = [
        'Vacaciones'        => $class_rrhh->count_permisos_by_type('Vacaciones', $fecha_desde, $fecha_hasta),
        'Compensatorios'    => $class_rrhh->count_permisos_by_type('Compensatorio', $fecha_desde, $fecha_hasta),
        'Citas médicas'     => $class_rrhh->count_permisos_by_type('Cita Medica', $fecha_desde, $fecha_hasta),
        'Enfermedad'        => $class_rrhh->count_permisos_by_type('Enfermedad', $fecha_desde, $fecha_hasta),
        'Teletrabajo'       => $class_rrhh->count_permisos_by_type('Teletrabajo', $fecha_desde, $fecha_hasta),
        'Duelo'             => $class_rrhh->count_permisos_by_type('Duelo', $fecha_desde, $fecha_hasta),
        'Tiempo sin pago'   => $class_rrhh->count_permisos_by_type('Tiempo sin pago', $fecha_desde, $fecha_hasta),
    ];

    // Métricas de uniformes (mismo período)
    $uniformes_total = $class_rrhh->count_uniformes($fecha_desde, $fecha_hasta);
    $uniformes_solicitado = $class_rrhh->count_uniformes_por_estado(1, $fecha_desde, $fecha_hasta);
    $uniformes_en_proceso = $class_rrhh->count_uniformes_por_estado(2, $fecha_desde, $fecha_hasta);
    $uniformes_entregado = $class_rrhh->count_uniformes_por_estado(3, $fecha_desde, $fecha_hasta);
    $uniformesChartData = [
        'Solicitado'   => $uniformes_solicitado,
        'En proceso'   => $uniformes_en_proceso,
        'Entregado'    => $uniformes_entregado,
    ];

    $dashboardTotals = [
        'Actualización de Datos'   => 41, // 41 - valor fijo
        'Cartas de trabajo'        => $cartas_trabajos,
        'Calamidades'              => $calamidades,
        'Permisos solicitados'     => $permisos,
        'Incapacidades'            => $incapacidades,
    ];

    require_once __DIR__ . '/../views/metricas.php';
    exit();
}

if (isset($_GET['organigrama'])) {
    require_once __DIR__ . '/../views/organigrama.php';
    exit();
}

if (isset($_GET['exportar_excel'])) {
    // Obtener filtros de fecha
    $fecha_desde = isset($_GET['fecha_desde']) && !empty($_GET['fecha_desde']) ? $_GET['fecha_desde'] : null;
    $fecha_hasta = isset($_GET['fecha_hasta']) && !empty($_GET['fecha_hasta']) ? $_GET['fecha_hasta'] : null;

    // Si no hay fechas especificadas, usar el mes actual por defecto
    if (!$fecha_desde && !$fecha_hasta) {
        $fecha_desde = date('Y-m-01');
        $fecha_hasta = date('Y-m-d');
    }

    // Obtener datos para exportar
    $cartas_trabajos = $class_rrhh->count_cartas_trabajo($fecha_desde, $fecha_hasta);
    $calamidades = $class_rrhh->count_calamidades($fecha_desde, $fecha_hasta);
    $permisos = $class_rrhh->count_permisos($fecha_desde, $fecha_hasta);
    $incapacidades = $class_rrhh->count_incapacidades($fecha_desde, $fecha_hasta);
    $vacaciones = $class_rrhh->count_type_permiso('Vacaciones', $fecha_desde, $fecha_hasta);
    $compensatorio = $class_rrhh->count_type_permiso('Compensatorio', $fecha_desde, $fecha_hasta);
    $cita_medica = $class_rrhh->count_type_permiso('Cita Medica', $fecha_desde, $fecha_hasta);
    $enfermedad = $class_rrhh->count_type_permiso('Enfermedad', $fecha_desde, $fecha_hasta);
    $tele_trabajo = $class_rrhh->count_type_permiso('Teletrabajo', $fecha_desde, $fecha_hasta);
    $duelo = $class_rrhh->count_type_permiso('Duelo', $fecha_desde, $fecha_hasta);
    $tiempo_sin_pago = $class_rrhh->count_type_permiso('Tiempo Sin Pago', $fecha_desde, $fecha_hasta);

    $uniformes_total_exp = $class_rrhh->count_uniformes($fecha_desde, $fecha_hasta);
    $uniformes_solicitado_exp = $class_rrhh->count_uniformes_por_estado(1, $fecha_desde, $fecha_hasta);
    $uniformes_en_proceso_exp = $class_rrhh->count_uniformes_por_estado(2, $fecha_desde, $fecha_hasta);
    $uniformes_entregado_exp = $class_rrhh->count_uniformes_por_estado(3, $fecha_desde, $fecha_hasta);

    // Configurar headers para descarga de Excel
    $filename = 'metricas_pcr_' . date('Y-m-d') . '.xls';
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: max-age=0');

    // Generar contenido Excel
    echo "<table border='1'>";
    echo "<tr><th colspan='2'><h2>Métricas PCR - Período: " . 
         ($fecha_desde ? date('d/m/Y', strtotime($fecha_desde)) : 'Inicio') . 
         " - " . 
         ($fecha_hasta ? date('d/m/Y', strtotime($fecha_hasta)) : 'Hoy') . 
         "</h2></th></tr>";
    
    echo "<tr><th>Concepto</th><th>Cantidad</th></tr>";
    echo "<tr><td>Actualización de Datos</td><td>41</td></tr>";
    echo "<tr><td>Solicitud de Carta de trabajo</td><td>" . $cartas_trabajos . "</td></tr>";
    echo "<tr><td>Solicitud de Calamidades</td><td>" . $calamidades . "</td></tr>";
    echo "<tr><td>Incapacidades Registradas</td><td>" . $incapacidades . "</td></tr>";
    echo "<tr><td>Total de Permisos Solicitados</td><td>" . $permisos . "</td></tr>";
    echo "<tr><td>Permisos por Vacaciones</td><td>" . $vacaciones . "</td></tr>";
    echo "<tr><td>Permisos por Compensatorios</td><td>" . $compensatorio . "</td></tr>";
    echo "<tr><td>Permisos por Citas médicas</td><td>" . $cita_medica . "</td></tr>";
    echo "<tr><td>Permisos por Enfermedad</td><td>" . $enfermedad . "</td></tr>";
    echo "<tr><td>Permisos por Teletrabajo</td><td>" . $tele_trabajo . "</td></tr>";
    echo "<tr><td>Permisos por Duelo</td><td>" . $duelo . "</td></tr>";
    echo "<tr><td>Permisos por Tiempo sin pago</td><td>" . $tiempo_sin_pago . "</td></tr>";
    echo "<tr><th colspan='2'>UNIFORMES</th></tr>";
    echo "<tr><td>Total solicitudes de uniformes</td><td>" . $uniformes_total_exp . "</td></tr>";
    echo "<tr><td>Uniformes - Solicitado</td><td>" . $uniformes_solicitado_exp . "</td></tr>";
    echo "<tr><td>Uniformes - En proceso</td><td>" . $uniformes_en_proceso_exp . "</td></tr>";
    echo "<tr><td>Uniformes - Entregado</td><td>" . $uniformes_entregado_exp . "</td></tr>";
    echo "</table>";
    exit();
}

require_once __DIR__ . '/../views/metricas.php';