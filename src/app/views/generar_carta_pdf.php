<?php
require_once __DIR__ . '../vendor/autoload.php';

use Dompdf\Dompdf;

// Verifica que lleguen los datos
if (!isset($_POST['solicitud_id'])) {
    die("Solicitud inválida.");
}

// Recibe los datos del formulario
$nombre = $_POST['nombre'] ?? '';
$cedula = $_POST['cedula'] ?? '';
$seguro = $_POST['seguro'] ?? '';
$fecha_ingreso = $_POST['fecha_ingreso'] ?? '';
$cargo = $_POST['cargo'] ?? '';
$salario = number_format($_POST['salario'] ?? 0, 2);
$desc_seguro = number_format($_POST['desc_seguro'] ?? 0, 2);
$desc_educativo = number_format($_POST['desc_educativo'] ?? 0, 2);
$desc_renta = number_format($_POST['desc_renta'] ?? 0, 2);
$descripcion = $_POST['descripcion'] ?? '';
$fecha_actual = date("d/m/Y");

// Contenido HTML de la carta
$html = "
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12pt; }
        h2 { text-align: center; }
        p { text-align: justify; line-height: 1.5; }
    </style>

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

    <p>Se expide la presente para los fines que estime convenientes.</p>

    <br><br><br>
    <p><strong>Departamento de Planilla</strong></p>
";

// Inicializa Dompdf
$dompdf = new Dompdf();
$dompdf->loadHtml($html);

// Configuración del papel
$dompdf->setPaper('A4', 'portrait');

// Renderiza el PDF
$dompdf->render();

// Muestra en el navegador
$dompdf->stream("carta_trabajo.pdf", ["Attachment" => false]); // true = descarga directa
exit;
