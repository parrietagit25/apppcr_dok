<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../models/Rrhh.php';

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

// Descuentos
$pdo = conexion();
$class = new Rrhh($pdo);
$otros_descuentos = $class->get_otros_descuentos_por_carta($_POST['solicitud_id']);

$html_dinamico = "";
if (!empty($otros_descuentos)) {
    $html_dinamico .= "<li><strong>Otros descuentos:</strong></li>";
    foreach ($otros_descuentos as $desc) {
        $acreedor = htmlspecialchars($desc['acreedor']);
        $monto = number_format($desc['monto'], 2);
        $html_dinamico .= "<li>$acreedor: B/. $monto</li>";
    }
}

// HTML
$html = "
<style>
    body { font-family: sans-serif; font-size: 12pt; }
    .encabezado { width: 100%; display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    .encabezado img { height: 80px; }
    .footer-img { text-align: center; margin-top: 40px; }
</style>

<div class='encabezado'>
    <img src='../../public/images/carta/logo.png' width='200' alt='Logo'>
    <div style='text-align: right; font-size: 10pt; margin-top: -80px;'>
        Tocumen Commercial Park<br>
        Tel: 279-2700<br>
        Grupopcr.com.pa
    </div>
</div>

<p>Panamá, $fecha_actual</p>
<p><strong>A quien pueda interesar:</strong></p>

<p>Por medio de la presente, hacemos constar que el(la) Sr(a). <strong>$nombre</strong>, con cédula <strong>$cedula</strong> y seguro social <strong>$seguro</strong>, labora en nuestra empresa desde el <strong>$fecha_ingreso</strong>, desempeñando el cargo de <strong>$cargo</strong>.</p>

<p>El salario mensual pactado es de B/. $salario, con las siguientes deducciones aproximadas:</p>
<ul>
    <li>Seguro Social: B/. $desc_seguro</li>
    <li>Seguro Educativo: B/. $desc_educativo</li>
    <li>Impuesto sobre la Renta: B/. $desc_renta</li>
    $html_dinamico
</ul>

<p>$descripcion</p>

<p>Se expide la presente para los fines que estime convenientes.</p>

<br><br><br>
<p><strong>Departamento de Planilla</strong></p>

<div class='footer-img'>
    <img src='../../public/images/carta/foot.png'>
</div>
";

// Generar PDF con mPDF
$mpdf = new \Mpdf\Mpdf([
    'default_font' => 'dejavusans',
    'tempDir' => __DIR__ . '/../../tmp/mpdf' // ✅ ruta absoluta desde el script actual
]);
$mpdf->WriteHTML($html);
$mpdf->Output("carta_trabajo.pdf", \Mpdf\Output\Destination::INLINE); // Muestra en navegador
exit;


