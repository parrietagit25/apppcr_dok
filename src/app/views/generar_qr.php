<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;

$codigo_empleado = $_GET['codigo'] ?? 'desconocido';

header('Content-Type: image/png');

echo Builder::create()
    ->writer(new PngWriter())
    ->data($codigo_empleado)
    ->size(150)
    ->margin(5)
    ->build()
    ->getString();
