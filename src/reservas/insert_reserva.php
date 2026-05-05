<?php
declare(strict_types=1);
header("Content-Type: application/json; charset=utf-8");
ini_set('display_errors', '0');
error_reporting(E_ALL);
register_shutdown_function(function () {
  $e = error_get_last();
  if ($e && in_array($e['type'], [E_ERROR,E_PARSE,E_CORE_ERROR,E_COMPILE_ERROR])) {
    http_response_code(500);
    error_log("[reservas] FATAL: {$e['message']} in {$e['file']}:{$e['line']}");
    echo json_encode(["error"=>"Fatal server error","detail"=>"check server logs"]);
  }
});

$host = getenv('DB_HOST') ?: 'db';
$usuario = getenv('DB_USER') ?: 'appuser';
$contraseña = getenv('DB_PASS') ?: '';
$dbname = getenv('DB_NAME') ?: 'apppcr';

try {
  $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $usuario, $contraseña, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ]);
} catch (Throwable $e) {
  http_response_code(500);
  error_log("[reservas] DB connect error: ".$e->getMessage());
  echo json_encode(["error"=>"DB connect"]);
  exit;
}

$raw = file_get_contents("php://input") ?: "";
$data = json_decode($raw, true);
if (!is_array($data)) { http_response_code(400); echo json_encode(["error"=>"Payload no es JSON válido"]); exit; }
if (!isset($data["commonid"])) { http_response_code(400); echo json_encode(["error"=>"Datos inválidos: falta commonid"]); exit; }

function clean($v, $type='s') {
  if ($v === "" || $v === null) return null;
  if ($type === 'i') return is_numeric($v) ? (int)$v : null;
  return trim((string)$v);
}

if (($data["datein"] ?? null) === "0000-00-00") $data["datein"] = null;

$params = [
  "commonid"        => clean($data["commonid"], 'i'),
  "resnumber"       => clean($data["resnumber"]),
  "ranumber"        => clean($data["ranumber"]),
  "company"         => clean($data["company"]),
  "dateout"         => clean($data["dateout"]),
  "datein"          => clean($data["datein"]),
  "reservedclass"   => clean($data["reservedclass"]),
  "customer"        => clean($data["customer"]),
  "dateadded"       => clean($data["dateadded"]),
  "locationcodeout" => clean($data["locationcodeout"]),
  "locationcodein"  => clean($data["locationcodein"]),
  "resstatus"       => clean($data["resstatus"], 'i'),
  "sourcecode"      => clean($data["sourcecode"]),
];

$sql = "INSERT INTO reservas (
  commonid, resnumber, ranumber, company, dateout, datein,
  reservedclass, customer, dateadded, locationcodeout,
  locationcodein, resstatus, sourcecode
) VALUES (
  :commonid, :resnumber, :ranumber, :company, :dateout, :datein,
  :reservedclass, :customer, :dateadded, :locationcodeout,
  :locationcodein, :resstatus, :sourcecode
)";
try {
  $st = $pdo->prepare($sql);
  $st->execute($params);
  echo json_encode(["success"=>true,"message"=>"Registro insertado"]);
} catch (Throwable $e) {
  http_response_code(400);
  error_log("[reservas] Insert error: ".$e->getMessage()." | payload=".substr(json_encode($params),0,500));
  echo json_encode(["error"=>"Error al insertar"]);
}
