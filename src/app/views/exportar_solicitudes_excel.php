<?php
require '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Configuración de conexión
$host = 'db';
$db   = 'apppcr';
$user = 'pedropcr';
$pass = 'Chicho1787$$$Chicho';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

// Consulta completa con collation + fix calamidades y tipo licencia en descripción
$sql = "
    SELECT 
        'Calamidad' AS tipo,
        e.codigo_empleado AS codigo,
        e.nombre,
        e.apellido,
        c.fecha_log,
        c.descripcion,
        c.file_add
    FROM calamidades c
    INNER JOIN empleados e 
        ON CAST(e.codigo_empleado AS UNSIGNED) = CAST(c.code_user AS UNSIGNED)
    WHERE c.stat = 1

    UNION ALL

    SELECT 
        'Carta de Trabajo' AS tipo,
        e.codigo_empleado AS codigo,
        e.nombre,
        e.apellido,
        ct.fecha_log,
        ct.descripcion,
        ct.file_add
    FROM carta_trabajo ct
    INNER JOIN empleados e 
        ON CONVERT(e.codigo_empleado USING utf8mb4) COLLATE utf8mb4_unicode_ci = 
           CONVERT(ct.code_user USING utf8mb4) COLLATE utf8mb4_unicode_ci
    WHERE ct.stat = 1

    UNION ALL

    SELECT 
        'Permiso' AS tipo,
        e.codigo_empleado AS codigo,
        e.nombre,
        e.apellido,
        sp.fecha_log,
        CONCAT(sp.tipo_licencia, ' - ', sp.descripcion) AS descripcion,
        sp.archivo_adjunto AS file_add
    FROM solicitud_permiso sp
    INNER JOIN empleados e 
        ON CONVERT(e.codigo_empleado USING utf8mb4) COLLATE utf8mb4_unicode_ci = 
           CONVERT(sp.code USING utf8mb4) COLLATE utf8mb4_unicode_ci
    WHERE sp.stat = 1

    ORDER BY fecha_log DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$solicitudes = $stmt->fetchAll();

// Crear Excel
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Solicitudes');

// Encabezados
$sheet->fromArray(
    ['Tipo', 'Código', 'Nombre', 'Apellido', 'Fecha', 'Descripción'],
    NULL,
    'A1'
);

// Llenar datos
$row = 2;
foreach ($solicitudes as $s) {
    $sheet->setCellValue("A$row", $s['tipo']);
    $sheet->setCellValue("B$row", $s['codigo']);
    $sheet->setCellValue("C$row", $s['nombre']);
    $sheet->setCellValue("D$row", $s['apellido']);
    $sheet->setCellValue("E$row", $s['fecha_log']);
    $sheet->setCellValue("F$row", $s['descripcion']);
    $row++;
}

// Descargar el archivo
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="solicitudes_registradas.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
