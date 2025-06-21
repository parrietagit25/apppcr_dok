<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../models/Rrhh.php';

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

$pdo = conexion();             // función definida en conexion.php
$class = new Rrhh($pdo);       // clase Rrhh que espera $pdo
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

$html = "
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12pt; margin: 40px; }
        .encabezado { width: 100%; }
        .logo { float: left; width: 40%; }
        .texto-superior { float: right; width: 55%; text-align: right; font-size: 10pt; line-height: 1.3; }
        .clear { clear: both; }
        .contenido { margin-top: 30px; }
        ul { line-height: 1.6; }
        .footer-img {
            margin-top: 60px;
            text-align: center;
        }
        .footer-img img {
            width: 100%;
            max-height: 80px;
        }
    </style>

    <div class='encabezado'>
        <div class='logo'>
            <img src='" . __DIR__ . "/../../public/images/carta/logo.png' width='150'>
        </div>
        <div class='texto-superior'>
            Tocumen Commercial Park<br>
            Tel: 279-2700<br>
            Grupopcr.com.pa
        </div>
    </div>
    <div class='clear'></div>

    <div class='contenido'>
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
    </div>

    <div class='footer-img'>
        <img src='" . __DIR__ . "/../../public/images/carta/foot.png' width='150'>
    </div>
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
