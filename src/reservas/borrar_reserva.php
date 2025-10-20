<?php
header("Content-Type: application/json");

// Configuración de la base de datos

$host = "db";  // Contenedor Docker MySQL
$usuario = "appuser";
$contraseña = "apppass";
$dbname = "apppcr";

// Conectar a MySQL
$conn = new mysqli($host, $usuario, $contraseña, $dbname);

if ($conn->connect_error) {
    die(json_encode(["error" => "Error de conexión a la base de datos: " . $conn->connect_error]));
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
    echo json_encode(["error" => "Error al Borrar : " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
