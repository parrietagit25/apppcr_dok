<?php
require '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Configuración de conexión
$host = 'db';
$db   = 'apppcr';
$user = 'appuser';
$pass = 'apppass';
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

$sql = "
    SELECT 
        sp.id,
        e.codigo_empleado AS codigo,
        e.nombre,
        e.apellido,
        e.nombre_departamento AS departamento,
        sp.fecha_log,
        sp.descripcion,
        sp.tipo_licencia,
        sp.fecha_inicio,
        sp.fecha_fin,
        sp.stat,
        sp.respuesta_jefe,
        sp.comentario_jefe,
        sp.archivo_adjunto AS file_add
    FROM solicitud_permiso sp
    INNER JOIN empleados e 
        ON CONVERT(e.codigo_empleado USING utf8mb4) COLLATE utf8mb4_unicode_ci = 
           CONVERT(sp.code USING utf8mb4) COLLATE utf8mb4_unicode_ci
    WHERE sp.tipo_licencia = 'Vacaciones'
    ORDER BY sp.fecha_log DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$vacaciones = $stmt->fetchAll();

// Crear Excel
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Vacaciones');

// Encabezados
$sheet->fromArray(
    [
        'ID', 'Código', 'Nombre', 'Apellido', 'Departamento', 'Fecha Solicitud', 'Descripción',
        'Tipo Licencia', 'Fecha Inicio', 'Fecha Fin', 'Estado', 'Respuesta Jefe', 'Comentario Jefe', 'Archivo'
    ],
    NULL,
    'A1'
);

// Llenar datos
$row = 2;
foreach ($vacaciones as $v) {
    $sheet->setCellValue("A$row", $v['id']);
    $sheet->setCellValue("B$row", $v['codigo']);
    $sheet->setCellValue("C$row", $v['nombre']);
    $sheet->setCellValue("D$row", $v['apellido']);
    $sheet->setCellValue("E$row", $v['departamento']);
    $sheet->setCellValue("F$row", $v['fecha_log']);
    $sheet->setCellValue("G$row", $v['descripcion']);
    $sheet->setCellValue("H$row", $v['tipo_licencia']);
    $sheet->setCellValue("I$row", $v['fecha_inicio']);
    $sheet->setCellValue("J$row", $v['fecha_fin']);
    $sheet->setCellValue("K$row", $v['stat']);
    $sheet->setCellValue("L$row", $v['respuesta_jefe']);
    $sheet->setCellValue("M$row", $v['comentario_jefe']);
    $sheet->setCellValue("N$row", $v['file_add']);
    $row++;
}

// Descargar el archivo
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="vacaciones.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;