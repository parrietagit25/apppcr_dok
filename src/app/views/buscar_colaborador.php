<?php 
header('Content-Type: application/json');
ob_start(); // Capturar cualquier salida inesperada

require_once __DIR__ . '/../../config/config.php';

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    echo json_encode(["error" => "Error en la conexión a la base de datos: " . $conn->connect_error]);
    exit;
}

$response = []; // Variable que contendrá la respuesta final

if (isset($_GET['codigo'])) {
    $codigo = trim($_GET['codigo']);

    $stmt = $conn->prepare("SELECT COUNT(*) FROM empleados WHERE codigo_empleado  = ?");
    $stmt->bind_param("s", $codigo);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM empleado_log WHERE codigo = ?");
        $stmt->bind_param("s", $codigo);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        $response = ["existe" => $count > 0];

    }else {
        $response = ["no_existe" => "El codigo de empleado no existe en la base de datos de RRHH"];
    }

}

if (isset($_GET['codigo_restores_pass']) && !empty($_GET['codigo_restores_pass'])) {
    $codigo = $_GET['codigo_restores_pass'];

    $stmt = $conn->prepare("SELECT email FROM empleados WHERE codigo_empleado = ?");
    $stmt->bind_param("s", $codigo);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $response = ["email" => $row['email']];
    } else {
        $response = ["error" => "El código no existe en la base de datos"];
    }
}

// Si no se recibió ningún parámetro válido
if (empty($response)) {
    $response = ["error" => "No se recibió un código válido"];
}

$conn->close(); // Cerrar la conexión solo al final

ob_end_clean(); // Eliminar cualquier salida inesperada antes de imprimir el JSON
echo json_encode($response);
exit;
?>
