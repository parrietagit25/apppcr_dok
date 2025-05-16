<?php
require '../../vendor/autoload.php';
require_once '../models/Rrhh.php'; // Asegúrate de usar el modelo correcto

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$model = new Rrhh();
$solicitudes = $model->obtenerSolicitudesUnificadas();

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Solicitudes');

// Encabezados
$sheet->fromArray(
    ['Tipo', 'Código', 'Nombre', 'Apellido', 'Fecha', 'Descripción'],
    NULL,
    'A1'
);

// Datos
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

// Cabeceras para descargar
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="solicitudes_registradas.xlsx"');
header('Cache-Control: max-age=0');

// Guardar en salida
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
