<?php
// Configuración de conexión a la base de datos externa (GoDaddy)
// ⚠️ IMPORTANTE: Cambiar 'localhost' por el hostname de GoDaddy si la BD está allí
define('DB_EXTERNAL_HOST', 'db'); // Cambiar a: 'grupopcr.com.pa' o la IP de GoDaddy si es externo
define('DB_EXTERNAL_NAME', 'apppcr');
define('DB_EXTERNAL_USER', 'appuser');
define('DB_EXTERNAL_PASS', 'apppass');
define('DB_EXTERNAL_CHARSET', 'utf8mb4');

// URL base de verificación
define('URL_BASE_VERIFICACION', 'https://grupopcr.com.pa/carta/');

// Configuración de encriptación
define('ENCRYPTION_KEY', 'PCR_2025_CARTA_VERIFICACION_KEY_SECURE'); // Cambiar por una clave más segura
define('ENCRYPTION_METHOD', 'AES-256-CBC');

// Configuración general
define('DIAS_EXPIRACION_CARTA', 365); // 1 año de vigencia
define('EMPRESA_NOMBRE', 'Automarket');
define('EMPRESA_RUC', ''); // Agregar si es necesario

// API de QR Code (usando servicio externo confiable)
define('QR_API_URL', 'https://api.qrserver.com/v1/create-qr-code/');
define('QR_SIZE', '300x300');
define('QR_FORMAT', 'png');

// API de sincronización con GoDaddy
define('API_GODADDY_URL', 'https://grupopcr.com.pa/carta/api_recibir_carta.php');
define('API_SECRET_KEY', 'c25854f002ca4cd308ff123e63df6c3786d84a5a9b7b7ef6a64cc96fdb25fad9'); // ⚠️ Cambiar por una clave segura única

