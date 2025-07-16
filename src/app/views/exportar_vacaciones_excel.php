<?php
require '../../vendor/autoload.php';
require_once __DIR__ . '/../core/Database.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$pdo = Database::connect();

$sql = "
    SELECT 
        sp.id,
        e.codigo_empleado,
        e.nombre,
        e.apellido,
        sp.fecha_log,
        sp.descripcion,
        sp.tipo_licencia,
        sp.fecha_inicio,
        sp.fecha_fin,
        sp.stat,
        sp.respuesta_jefe,
        sp.comentario_jefe,
        sp.archivo_adjunto
    FROM solicitud_permiso sp
    INNER JOIN empleados e 
        ON CONVERT(e.codigo_empleado USING utf8mb4) COLLATE utf8mb4_unicode_ci = 
           CONVERT(sp.code USING utf8mb4) COLLATE utf8mb4_unicode_ci
    WHERE sp.tipo_licencia = 'Vacaciones'
    ORDER BY sp.fecha_log DESC
";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$vacaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Vacaciones');

// Encabezados
$headers = [
    'ID', 'Código', 'Nombre', 'Apellido', 'Fecha Solicitud', 'Descripción',
    'Tipo Licencia', 'Fecha Inicio', 'Fecha Fin', 'Estado', 'Respuesta Jefe', 'Comentario Jefe', 'Archivo'
];
$sheet->fromArray($headers, NULL, 'A1');

// Datos
$row = 2;
foreach ($vacaciones as $v) {
    $sheet->fromArray([
        $v['id'],
        $v['codigo_empleado'],
        $v['nombre'],
        $v['apellido'],
        $v['fecha_log'],
        $v['descripcion'],
        $v['tipo_licencia'],
        $v['fecha_inicio'],
        $v['fecha_fin'],
        $v['stat'],
        $v['respuesta_jefe'],
        $v['comentario_jefe'],
        $v['archivo_adjunto']
    ], NULL, 'A' . $row++);
}

// Descargar
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="vacaciones.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
