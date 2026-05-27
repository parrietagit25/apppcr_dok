<?php
/**
 * Health check liviano: solo confirma que PHP responde (sin MySQL).
 * Uso: monitor externo para distinguir PHP/Nginx vs base de datos.
 */

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate');

http_response_code(200);
echo json_encode(['status' => 'ok'], JSON_UNESCAPED_UNICODE);
