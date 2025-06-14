<?php
require('../fpdf/fpdf.php');

// Verificar si se enviaron los datos necesarios
if (!isset($_POST['solicitud_id'])) {
    die("Solicitud inválida.");
}

$id = $_POST['solicitud_id'];
$nombre = $_POST['nombre'] ?? '';
$cedula = $_POST['cedula'] ?? '';
$seguro = $_POST['seguro'] ?? '';
$fecha_ingreso = $_POST['fecha_ingreso'] ?? '';
$cargo = $_POST['cargo'] ?? '';
$salario = number_format(floatval($_POST['salario'] ?? 0), 2);
$desc_seguro = number_format(floatval($_POST['desc_seguro'] ?? 0), 2);
$desc_educativo = number_format(floatval($_POST['desc_educativo'] ?? 0), 2);
$desc_renta = number_format(floatval($_POST['desc_renta'] ?? 0), 2);
$descripcion = $_POST['descripcion'] ?? '';
$fecha_actual = date("d/m/Y");

class PDF extends FPDF
{
    function Header()
    {
        // Encabezado
        $this->SetFont('Arial','B',14);
        $this->Cell(0,10,utf8_decode('GRUPO PCR'),0,1,'C');
        $this->SetFont('Arial','',12);
        $this->Cell(0,10,utf8_decode('Carta de Trabajo'),0,1,'C');
        $this->Ln(5);
    }

    function Footer()
    {
        // Pie de página
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Grupo PCR - Documento generado automaticamente',0,0,'C');
    }
}

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial','',12);

// Cuerpo
$pdf->MultiCell(0,8,utf8_decode("Panamá, $fecha_actual"));
$pdf->Ln(10);

$pdf->MultiCell(0,8,utf8_decode("A quien pueda interesar:"));
$pdf->Ln(10);

$texto = "
Por medio de la presente, hacemos constar que el(la) Sr(a). $nombre, con cédula $cedula y seguro social $seguro, 
labora en nuestra empresa desde el $fecha_ingreso, desempeñando el cargo de $cargo.

El salario mensual pactado es de B/. $salario, con las siguientes deducciones aproximadas: 
Seguro Social B/. $desc_seguro, Seguro Educativo B/. $desc_educativo, Impuesto sobre la Renta B/. $desc_renta.

$descripcion

Se expide la presente para los fines que estime convenientes.
";

$pdf->MultiCell(0,8,utf8_decode($texto));
$pdf->Ln(20);

// Pie sin firma
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,10,utf8_decode('Departamento de Planilla'),0,1,'L');

$pdf->Output("I", "carta_trabajo_$id.pdf");
exit;
