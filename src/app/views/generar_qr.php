<?php
require_once __DIR__ . '/../libs/phpqrcode/qrlib.php';

$codigo = $_GET['codigo'] ?? 'sin-codigo';
$contenido = "https://apppcr.net/validar_empleado.php?codigo=" . urlencode($codigo);

header('Content-Type: image/png');
QRcode::png($contenido, false, QR_ECLEVEL_L, 4);
