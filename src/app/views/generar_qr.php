<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use Endroid\QrCode\QrCode;
use Endroid\QrCode\ErrorCorrectionLevel;

$codigo = $_GET['codigo'] ?? 'sin-codigo';
$contenido = "https://apppcr.net/validar_empleado.php?codigo=" . urlencode($codigo);

// Crear código QR
$qrCode = new QrCode($contenido);
$qrCode->setSize(300);
$qrCode->setMargin(10);
$qrCode->setEncoding('UTF-8');
$qrCode->setErrorCorrectionLevel(new ErrorCorrectionLevel(ErrorCorrectionLevel::HIGH));
$qrCode->setRoundBlockSize(true);
$qrCode->setValidateResult(false);

// Salida de imagen PNG
header('Content-Type: ' . $qrCode->getContentType());
echo $qrCode->writeString();
