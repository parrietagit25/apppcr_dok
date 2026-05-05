<?php
header("Content-Type: application/json");

// Configuración de la base de datos

$host = getenv('DB_HOST') ?: 'db';
$usuario = getenv('DB_USER') ?: 'appuser';
$contraseña = getenv('DB_PASS') ?: '';
$dbname = getenv('DB_NAME') ?: 'apppcr';

// Conectar a MySQL
$conn = new mysqli($host, $usuario, $contraseña, $dbname);

if ($conn->connect_error) {
    error_log("[reservas] DB connect error: " . $conn->connect_error);
    die(json_encode(["error" => "Error de conexión a la base de datos"]));
}

// Recibir datos JSON
$data = json_decode(file_get_contents("php://input"), true);
/*
if (!isset($data["commonid"])) {
    echo json_encode(["error" => "Datos inválidos"]);
    exit;
}
    */

// Preparar la consulta SQL
$stmt = $conn->prepare("DELETE FROM reservas");

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Registros Eliminados"]);
} else {
    error_log("[reservas] Delete error: " . $stmt->error);
    echo json_encode(["error" => "Error al borrar"]);
}

$stmt->close();
$conn->close();
?>
