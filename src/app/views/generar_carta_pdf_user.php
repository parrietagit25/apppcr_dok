<?php
session_start();
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../models/Rrhh.php';

use Dompdf\Dompdf;


$class = new Rrhh();

if (!isset($_SESSION['code']) || !isset($_GET['id'])) {
    die("Acceso denegado.");
}

$id_carta = $_GET['id'];
$datos = $class->get_datos_formulario_carta($id_carta);

if (!$datos) {
    die("Carta no encontrada o no disponible.");
}

$fecha_actual = date("d/m/Y");
extract($datos);

$html = "
    <style> body { font-family: DejaVu Sans, sans-serif; font-size: 12pt; } </style>
    <p>Panamá, $fecha_actual</p>
    <p><strong>A quien pueda interesar:</strong></p>
    <p>Por medio de la presente, hacemos constar que el(la) Sr(a). <strong>$nombre</strong>, con cédula <strong>$cedula</strong> y seguro social <strong>$seguro</strong>, labora en nuestra empresa desde el <strong>$fecha_ingreso</strong>, desempeñando el cargo de <strong>$cargo</strong>.</p>
    <p>El salario mensual pactado es de B/. $salario, con las siguientes deducciones aproximadas:</p>
    <ul>
        <li>Seguro Social: B/. $desc_seguro</li>
        <li>Seguro Educativo: B/. $desc_educativo</li>
        <li>Impuesto sobre la Renta: B/. $desc_renta</li>
    </ul>
    <p>$descripcion</p>
    <br><br>
    <p><strong>Departamento de Planilla</strong></p>
";

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream('Carta_Trabajo_' . $nombre . '.pdf', ['Attachment' => true]);
exit;
