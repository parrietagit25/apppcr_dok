<?php
declare(strict_types=1);
header("Content-Type: application/json; charset=utf-8");
ini_set('display_errors', '0');
error_reporting(E_ALL);

// Responder JSON incluso si hay fatal
register_shutdown_function(function () {
  $e = error_get_last();
  if ($e && in_array($e['type'], [E_ERROR,E_PARSE,E_CORE_ERROR,E_COMPILE_ERROR])) {
    http_response_code(500);
    error_log("[clientes] FATAL: {$e['message']} in {$e['file']}:{$e['line']}");
    echo json_encode(["error"=>"fatal","detail"=>"check server logs"]);
  }
});

$host = "db";  // Contenedor Docker MySQL
$user = "appuser";
$pass = "apppass";
$db   = "apppcr";

try {
  $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ]);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(["error"=>"db_connect", "detail"=>$e->getMessage()]);
  exit;
}

// Solo PTY, hoy, estado 20, nombres no vacíos
$sql = "
SELECT
  UPPER(customer) AS customer,
  MAX(CASE WHEN sourcecode = '210' THEN '210' ELSE '0' END) AS sourcecode
FROM reservas
WHERE
  dateout = CURDATE()
  AND customer IS NOT NULL AND customer <> ''
  AND locationcodeout = 'PTY'
  AND resstatus = 20
GROUP BY UPPER(customer)
ORDER BY customer ASC
";

try {
  $rows = $pdo->query($sql)->fetchAll();
  echo json_encode($rows, JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
  http_response_code(500);
  error_log("[clientes] SQL error: ".$e->getMessage());
  echo json_encode(["error"=>"sql", "detail"=>$e->getMessage()]);
}
