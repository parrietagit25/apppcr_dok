<?php
require_once __DIR__ . '/../libs/phpqrcode/qrlib.php';

$codigo = $_GET['codigo'] ?? 'sin-codigo';

// Puedes usar contenido simple o una URL personalizada:
$contenido = "https://apppcr.net/validar_empleado.php?codigo=" . urlencode($codigo);

// Encabezado para indicar que se devuelve una imagen
header('Content-Type: image/png');

// Generar QR directamente en la salida
QRcode::png($contenido, false, QR_ECLEVEL_L, 4);
