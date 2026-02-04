<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../config/conexion.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$pdo = conexion();

$sql = "
    SELECT 
        u.id,
        u.codigo_empleado,
        e.nombre,
        e.apellido,
        e.nombre_departamento,
        e.nombre_cargo,
        u.tipo,
        u.talla,
        u.cantidad,
        u.stat,
        u.fecha_log,
        u.fecha_proceso,
        u.fecha_entrega,
        u.observacion
    FROM uniformes u
    INNER JOIN empleados e 
    ON CONVERT(u.codigo_empleado USING utf8mb4) COLLATE utf8mb4_unicode_ci = 
       CONVERT(e.codigo_empleado USING utf8mb4) COLLATE utf8mb4_unicode_ci
    WHERE u.stat IN (1, 2, 3)
    ORDER BY u.stat ASC, u.fecha_log DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$uniformes = $stmt->fetchAll(PDO::FETCH_ASSOC);

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Solicitudes Uniformes');

$estados = [1 => 'Solicitado', 2 => 'En Proceso', 3 => 'Entregado'];

// Encabezados
$sheet->fromArray(
    [
        'Código', 'Nombre', 'Apellido', 'Departamento', 'Cargo', 'Tipo', 'Talla', 'Cant.', 'Estado', 'Fecha Solicitud', 'Fecha Proceso', 'Fecha Entrega', 'Observación'
    ],
    null,
    'A1'
);

$row = 2;
foreach ($uniformes as $u) {
    $sheet->setCellValue("A$row", $u['codigo_empleado']);
    $sheet->setCellValue("B$row", $u['nombre']);
    $sheet->setCellValue("C$row", $u['apellido']);
    $sheet->setCellValue("D$row", $u['nombre_departamento'] ?? '');
    $sheet->setCellValue("E$row", $u['nombre_cargo'] ?? '');
    $sheet->setCellValue("F$row", ucfirst($u['tipo'] ?? ''));
    $sheet->setCellValue("G$row", $u['talla']);
    $sheet->setCellValue("H$row", $u['cantidad'] ?? 1);
    $sheet->setCellValue("I$row", $estados[$u['stat']] ?? 'Desconocido');
    $sheet->setCellValue("J$row", $u['fecha_log']);
    $sheet->setCellValue("K$row", $u['fecha_proceso'] ?? '');
    $sheet->setCellValue("L$row", $u['fecha_entrega'] ?? '');
    $sheet->setCellValue("M$row", $u['observacion'] ?? '');
    $row++;
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="solicitudes_uniformes.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
