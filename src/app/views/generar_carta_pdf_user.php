<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../models/Rrhh.php';

use Mpdf\Mpdf;

session_start();

// Verificar sesión e ID
if (!isset($_SESSION['code']) || !isset($_GET['id'])) {
    die("Acceso denegado.");
}

// Conexión y clase
$pdo = conexion();
$class = new Rrhh($pdo);

// Obtener datos
$id_carta = $_GET['id'];
$datos = $class->get_datos_formulario_carta($id_carta);
$otros_descuentos = $class->get_otros_descuentos_por_carta($id_carta);

// Validar datos
if (!$datos) {
    die("Carta no encontrada o no disponible.");
}

// Extraer datos
$fecha_actual = date("d/m/Y");
extract($datos); // $nombre, $apellido, $cedula, etc.

$html_dinamico = "";
if (!empty($otros_descuentos)) {
    $html_dinamico .= "<li><strong>Otros descuentos:</strong></li>";
    foreach ($otros_descuentos as $desc) {
        $acreedor = htmlspecialchars($desc['acreedor']);
        $monto = number_format($desc['monto'], 2);
        $html_dinamico .= "<li>$acreedor: B/. $monto</li>";
    }
}

// Ruta a las imágenes
$path_logo = __DIR__ . '/../../public/images/carta/logo.png';
$path_footer = __DIR__ . '/../../public/images/carta/foot.png';

$logo_src = 'file://' . realpath($path_logo);
$footer_src = 'file://' . realpath($path_footer);

// Construcción del HTML
$html = "
<style>
    body { font-family: sans-serif; font-size: 12pt; line-height: 1.5; }
    .encabezado { width: 100%; display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    .encabezado img { height: 80px; }
    .footer-img { text-align: center; margin-top: 40px; }
    ul { padding-left: 20px; line-height: 1.6; }
</style>

<div class='encabezado'>
    <img src='$logo_src' width='200' alt='Logo'>
    <div style='text-align: right; font-size: 10pt; position: relative; top: -60px;'>
        Tocumen Commercial Park<br>
        Tel: 279-2700<br>
        Grupopcr.com.pa
    </div>
</div>

<p>Panamá, $fecha_actual</p>

<p><strong>A quien pueda interesar:</strong></p>

<p>Por medio de la presente, hacemos constar que el(la) Sr(a). <strong>$nombre $apellido</strong>, con cédula <strong>$cedula</strong> y seguro social <strong>$seguro</strong>, labora en nuestra empresa desde el <strong>$fecha_ingreso</strong>, desempeñando el cargo de <strong>$cargo</strong>.</p>

<p>El salario mensual pactado es de B/. $salario, con las siguientes deducciones aproximadas:</p>
<ul>
    <li>Seguro Social: B/. $desc_seguro</li>
    <li>Seguro Educativo: B/. $desc_educativo</li>
    <li>Impuesto sobre la Renta: B/. $desc_renta</li>
    $html_dinamico
</ul>

<p>$descripcion</p>

<br><br>
<p><strong>Departamento de Planilla</strong></p>

<div class='footer-img'>
    <img src='$footer_src'>
</div>
";

// Generar PDF con mPDF
$mpdf = new Mpdf([
    'default_font' => 'dejavusans',
    'tempDir' => __DIR__ . '/../../tmp/mpdf' // ✅ ruta temporal personalizada
]);

$mpdf->WriteHTML($html);
$filename = 'Carta_Trabajo_' . preg_replace('/[^a-zA-Z0-9]/', '_', $nombre) . '.pdf';
$mpdf->Output($filename, \Mpdf\Output\Destination::DOWNLOAD); // Descargar directamente
exit;
