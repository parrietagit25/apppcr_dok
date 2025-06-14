<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

$codigo = $_GET['codigo'] ?? 'sin-codigo';
$contenido = "https://apppcr.net/validar_empleado.php?codigo=" . urlencode($codigo);

$qrCode = new QrCode($contenido);
$qrCode->setSize(200);

header('Content-Type: image/png');
$writer = new PngWriter();
echo $writer->write($qrCode)->getString();
