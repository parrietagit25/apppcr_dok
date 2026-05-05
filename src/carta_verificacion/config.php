<?php
if (!function_exists('carta_env')) {
    function carta_env(string $key, string $default = ''): string
    {
        $value = getenv($key);
        return ($value === false || $value === '') ? $default : $value;
    }
}

// Configuración de conexión a la base de datos externa (GoDaddy)
// ⚠️ IMPORTANTE: Cambiar 'localhost' por el hostname de GoDaddy si la BD está allí
define('DB_EXTERNAL_HOST', carta_env('DB_EXTERNAL_HOST', 'db')); // Cambiar a: 'grupopcr.com.pa' o la IP de GoDaddy si es externo
define('DB_EXTERNAL_NAME', carta_env('DB_EXTERNAL_NAME', 'apppcr'));
define('DB_EXTERNAL_USER', carta_env('DB_EXTERNAL_USER', 'appuser'));
define('DB_EXTERNAL_PASS', carta_env('DB_EXTERNAL_PASS', ''));
define('DB_EXTERNAL_CHARSET', carta_env('DB_EXTERNAL_CHARSET', 'utf8mb4'));

// URL base de verificación
define('URL_BASE_VERIFICACION', 'https://grupopcr.com.pa/carta/');

// Configuración de encriptación
define('ENCRYPTION_KEY', carta_env('ENCRYPTION_KEY', '')); // Configurar en variables de entorno
define('ENCRYPTION_METHOD', 'AES-256-CBC');

// Configuración general
define('DIAS_EXPIRACION_CARTA', 365); // 1 año de vigencia
define('EMPRESA_NOMBRE', 'Grupo PCR');
define('EMPRESA_RUC', ''); // Agregar si es necesario

// API de QR Code (usando servicio externo confiable)
define('QR_API_URL', 'https://api.qrserver.com/v1/create-qr-code/');
define('QR_SIZE', '300x300');
define('QR_FORMAT', 'png');

// API de sincronización con GoDaddy
define('API_GODADDY_URL', 'https://grupopcr.com.pa/carta/api_recibir_carta.php');
define('API_SECRET_KEY', carta_env('API_SECRET_KEY', '')); // ⚠️ Definir en variables de entorno

